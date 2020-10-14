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
    const IMAGE_DIR = '/QRImage/';

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public static function getPath()
    {
        $imageUrl = self::IMAGE_DIR . Yii::$app->request->get()['id'] . '.png';
        $imagePath = Yii::getAlias('@webroot') . $imageUrl;
        $imageDir = Yii::getAlias('@webroot') . self::IMAGE_DIR;
        if (file_exists($imageDir) == false) {
            try {
                if (mkdir($imageDir, 0700) == false) {
                    throw new Exception();
                }
            } catch (Exception $e) {
                throw new NotFoundHttpException('QRImage: ошибка записи папки');
            }
        }
        if (file_exists($imagePath) == false) {
            $qrCode = (new QrCode(Url::to('', true)))
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