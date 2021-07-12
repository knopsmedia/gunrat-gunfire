<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Service;

use Gunratbe\App\Repository\KeyValueRepository;
use Knops\ShopifyClient\ApiClient;
use Knops\Utilities\Factory\DateTimeFactory;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class CreateSupplierBuyOrder
{
    private string $key = 'orders_last_synced_at';
    private bool $debug = false;

    public function __construct(
        private ApiClient          $shopifyApi,
        private KeyValueRepository $keyValueRepository,
        private Mailer             $mailer,
        private array              $recipients,
    ) {}

    public function create()
    {
        $supplierBuyLines = $this->getExcelContents();
        if (empty($supplierBuyLines)) return;

        $this->sendMail($supplierBuyLines);
        $this->updateLastFetchDate();
    }

    private function getExcelContents(): array
    {
        if ($this->debug) {
            return [
                ['F1234', 1, '#1001'],
                ['F1235', 2, '#1001'],
                ['G1234', 2, '#1002'],
                ['G1235', 1, '#1002'],
            ];
        }

        $orders = $this->shopifyApi->orders()->findUnfulfilledOrders($this->getLastFetchDate());
        $lines = [];

        foreach ($orders as $order) {
            foreach ($order->line_items as $lineItem) {
                $lines[] = [$lineItem->sku, $lineItem->quantity, $order->name];
            }
        }

        return $lines;
    }

    private function getLastFetchDate(): \DateTimeInterface
    {
        $lastFetchDate = $this->keyValueRepository->get($this->key);
        if (null === $lastFetchDate) $lastFetchDate = DateTimeFactory::now();

        return $lastFetchDate;
    }

    /**
     * @param array $supplierBuyLines
     */
    private function sendMail(array $supplierBuyLines): void
    {
        $message = (new Message())
            ->setFrom('no-reply@knopsmedia.be')
            ->setSubject('GUNRAT | Gunfire import bestand voor nieuwste orders van ' . $this->getLastFetchDate()->format('d.m.Y'))
            ->setBody('In bijlage');

        $message->addAttachment(
            sprintf('gunfire-import-%s.xls', $this->getLastFetchDate()->format('Y.m.d')),
            stream_get_contents($this->createSpreadsheetFile($supplierBuyLines))
        );

        foreach ($this->recipients as $recipient) {
            $message->addTo($recipient);
        }

        $this->mailer->send($message);
    }

    private function createSpreadsheetFile(array $data)
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray($data, null, 'A2');

        $fh = fopen('php://temp', 'rb+');
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save($fh);

        // set file pointer at the beginning
        rewind($fh);

        return $fh;
    }

    private function updateLastFetchDate()
    {
        $this->keyValueRepository->set($this->key, DateTimeFactory::now());
    }
}