<?php 

namespace mata\media\controllers;

use Aws\S3\S3Client;
use mata\keyvalue\models\KeyValue;
use mata\media\models\Media;
use yii\helpers\Json;
use mata\base\DocumentId;
use mata\arhistory\models\Revision;

class S3Controller extends \mata\web\Controller {

	// NOT THE RIGHT TO PLACE THESE - WHERE THOUGH?
	const S3_KEY = "S3_KEY";
	const S3_SECRET = "S3_SECRET";
	const S3_BUCKET = "S3_BUCKET";
	const S3_ENDPOINT = "S3_ENDPOINT";
	const S3_FOLDER = "S3_FOLDER";
	const S3_REGION = "S3_REGION";

	public function actionSignature() {

		$this->setResponseContentType("application/json");

		$s3Key = KeyValue::findbyKey(self::S3_KEY);
		$s3Secret = KeyValue::findbyKey(self::S3_SECRET);
		$s3Bucket = KeyValue::findbyKey(self::S3_BUCKET);

		// Instantiate the S3 client with your AWS credentials
		$s3Client = S3Client::factory(array(
			'key'    => $s3Key,
			'secret' => $s3Secret,
			'region' => KeyValue::findbyKey(self::S3_REGION)
			));

		$policyDocument = '{"expiration":"2100-01-01T00:00:00Z","conditions":[{"bucket": "' . $s3Bucket . '"},{"acl": "public-read"}, ["starts-with", "$key", ""], ["starts-with", "$Content-Type", ""], 
		["starts-with", "$success_action_status", ""], ["starts-with", "$x-amz-meta-qqfilename", ""]]}';

		$encodedPolicy = base64_encode($policyDocument);
		$signature = base64_encode(hash_hmac(
			'sha1',
			$encodedPolicy,
			$s3Secret,
			true
			));


		$response = array('policy' => $encodedPolicy, 'signature' => $signature);
		echo json_encode($response);
	}

	public function actionUploadSuccessful() {

		$s3Endpoint = KeyValue::findByKey(self::S3_ENDPOINT);
		$s3Bucket = KeyValue::findByKey(self::S3_BUCKET);

		$imageURL = $s3Endpoint .  $s3Bucket  . "/" . urlencode(\Yii::$app->getRequest()->post("key"));
		$documentId = \Yii::$app->getRequest()->get("documentId");

		// if($media = Media::find()->where(["For" => $documentId])->one())
		// 	$media->delete();

		// $pattern = '/([a-zA-Z\\\]*)-([a-zA-Z0-9]*)(::)?([a-zA-Z0-9]*)?/';
		// preg_match($pattern, $documentId, $matches);	

		// $model = Media::find()->forItem($documentId)->one();

		// if ($model == null)
		// 	$model = new Media();

		// if(!empty($matches) && empty($matches[2])) {
		// 	$pk = uniqid('tmp_');
		// 	if(!empty($matches[4]))
		// 		$pk .= "::" . $matches[4];

		// 	$documentId = $matches[1] . "-" . $pk;
		// 	$model->disableVersioning();
		// }

		// $mediaWidth = 0; 
		// $mediaHeight = 0;
		// $mimeType = "default";

		$imageAttributes = getimagesize($imageURL);

		if ($imageAttributes != null) {
			$mediaWidth = $imageAttributes[0];
			$mediaHeight = $imageAttributes[1];
			$mimeType = $imageAttributes['mime'];
		} else {
			$ch = curl_init($imageURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_exec($ch);
			$mimeType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		}


		// $linkedModel = new DocumentId($documentId);
		// $linkedModel = $linkedModel->getModel();

		// $model->attributes = array(
		// 	"Name" => \Yii::$app->getRequest()->post("name"),
		// 	"For" => $documentId,
		// 	"URI" => $imageURL,
		// 	"Width" => $mediaWidth,
		// 	"Height" => $mediaHeight,
		// 	"MimeType" => $mimeType
		// 	);

		// if ($model->save() == false)
		// 	throw new \yii\web\HttpException(500, $model->getTopError());


		// // align revisions with base model
		// if ($linkedModel != null) {

		// 	$linkedModelRevision = 1;

		// 	$existingRevision = $model->getLatestRevision();

		// 	if ($existingRevision)
		// 		$linkedModelRevision = $existingRevision->Revision + 1;
			
		// 	$oldRevision = Revision::find()->where(["DocumentId" => $existingRevision->DocumentId, "Revision" => $linkedModelRevision])->one();

		// 	if ($oldRevision)
		// 		$oldRevision->delete();

		// 	$revision = $model->getLatestRevision();
		// 	$revision->Revision = $linkedModelRevision;

		// 	if ($revision->save() == false)
		// 		throw new \yii\web\HttpException(500, $revision->getTopError());
		// }


		$mediaResponse = [
			// 'Id' => time(), // Id is required by fineUploader
			'Name' => \Yii::$app->getRequest()->post("name"),
			'URI' => $imageURL,
			'DocumentId' => $documentId,
			'Width' => $mediaWidth,
			'Height' => $mediaHeight,
			'MimeType' => $mimeType,
			// 'Extra' => $model->Extra,
		];
		
		$this->setResponseContentType("application/json");
		echo Json::encode($mediaResponse);
	}

	public function actionSetRandomFileName() {
		$this->setResponseContentType("application/json");
		echo Json::encode($model);
	}

}