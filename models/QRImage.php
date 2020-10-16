<?php


namespace app\models;


use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;
use Da\QrCode\QrCode;
use Exception;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class QRImage
{
    const IMAGE_DIR = 'qr-image';
    const QUANTITY = 100;   // кол-во файлов в подпапке

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public static function getUrl()
    {
        $id = Yii::$app->request->get()['id'];
        $idDir = (int) ($id / self::QUANTITY);
        $imageUrl = '/' . self::IMAGE_DIR . '/' . $idDir . '/' . $id . '.png';
        $webRoot = Yii::getAlias('@webroot');   // A:/OSPanelWinXP/domains/yii.loc/web
        $imagePath = $webRoot . $imageUrl ;

        $imageDirPath = $webRoot . '/' . self::IMAGE_DIR;
        if (file_exists($imageDirPath) == false) {
            try {
                if (mkdir($imageDirPath, 0700) == false) {
                    throw new Exception();
                }
            } catch (Exception $e) {
                throw new NotFoundHttpException('QRImage: ошибка записи папки');
            }
        }

        $idDirPath = $webRoot . '/' . self::IMAGE_DIR . '/' . $idDir;
        if (file_exists($idDirPath) == false) {
            try {
                if (mkdir($idDirPath, 0700) == false) {
                    throw new Exception();
                }
            } catch (Exception $e) {
                throw new NotFoundHttpException('QRImage: ошибка записи папки');
            }
        }

        if (file_exists($imagePath) == false) {
            $qrCode = (new QrCode(Url::to(['/device/view', 'id' => $id], true)))
                ->setSize(200)
                ->setMargin(5)
                ->setErrorCorrectionLevel(ErrorCorrectionLevelInterface::HIGH);
            try {
                if ($qrCode->writeFile($imagePath) == false) {
                    throw new Exception();
                }
            } catch (Exception $e) {
                throw new NotFoundHttpException('QRImage: ошибка записи файла');
            }
        }
        return $imageUrl;
    }
}