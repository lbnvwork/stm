<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 22.04.19
 * Time: 15:07
 */

namespace Office\Service;

use App\Service\FlashMessage;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkFirm;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\VarDumper\VarDumper;
use Zend\Diactoros\UploadedFile;

/**
 * Class SaveFileService
 * Для загрузки файлов @todo отвязать от прошивки, сделать более универсальным;
 *
 * @package Office\Service
 */
class SaveFileService
{
    private $entityManager;

    public const ALLOWED_FIRMWARE_FORMAT_DOC = [
        'bin' => [
            'application/octet-stream',
        ],
    ];

    /**
     * SaveFileService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Загружает файл
     *
     * @param ServerRequestInterface $request
     * @param LkFirm $firmware
     *
     * @return bool
     */
    public function saveFile(ServerRequestInterface $request, LkFirm $firmware)
    {
        $uploadedFile = $this->getFile($request, self::ALLOWED_FIRMWARE_FORMAT_DOC, 'file');
        if ($uploadedFile) {
            $ext = strtolower(pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION));
            $sitePatch = '/firmwares/'.$firmware->getUser()->getId();
            $patch = ROOT_PATH.'data'.$sitePatch;
            if (!is_dir($patch)) {
                if (!mkdir($patch, 0775, true) && !is_dir($patch)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $patch));
                }
            }
            $fileName = md5($uploadedFile->getClientFilename().time()).'.'.$ext;
            $uploadedFile->moveTo($patch.'/'.$fileName);
            $firmware->setFilename('docs'.$sitePatch.'/'.$fileName);
            $firmware->setImageType($ext);
            $firmware->setSize($uploadedFile->getSize());
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получает файл
     *
     * @param ServerRequestInterface $request
     * @param array $allowedFormatDoc
     * @param string $fileField
     *
     * @return UploadedFile|null
     */
    public function getFile(ServerRequestInterface $request, array $allowedFormatDoc, string $fileField): ?UploadedFile
    {
        $files = $request->getUploadedFiles();

        /** @var FlashMessage $flashMessage */
        $flashMessage = $request->getAttribute(FlashMessage::class);

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $files[$fileField];
        if ($uploadedFile->getError()) {
            if ($uploadedFile->getClientFilename() != null) {
                $flashMessage->addWarningMessage('Ошибка загрузки файла');
            }
            return null;
        }
        $ext = strtolower(pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION));
        if (isset($allowedFormatDoc[$ext]) && \in_array($uploadedFile->getClientMediaType(), $allowedFormatDoc[$ext], true)) {
            return $uploadedFile;
        } else {
            $flashMessage->addWarningMessage('Неразрешенный формат файла');
            return null;
        }
    }
}
