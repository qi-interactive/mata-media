<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\media\models;

use Yii;
use mata\db\ActiveRecord;
use mata\db\ActiveQuery;
use mata\arhistory\behaviors\HistoryBehavior;
use matacms\environment\behaviors\EnvironmentBehavior;

/**
 * This is the model class for table "{{%mata_media}}".
 *
 * @property integer $Id
 * @property string $DateCreated
 * @property string $Name
 * @property string $URI
 * @property integer $Width
 * @property integer $Height
 * @property string $MimeType
 * @property string $Extra
 */
class Media extends \matacms\db\ActiveRecord {

    public function behaviors() {
        return [
            HistoryBehavior::className(),
            EnvironmentBehavior::className()
        ];
    }

    public static function tableName()
    {
        return '{{%media2}}';
    }

    public function rules() {
        return [
        [['Name', 'MimeType', 'For'], 'required'],
        [['URI', 'Extra'], 'string'],
        [['Width', 'Height'], 'integer'],
        [['Name'], 'string', 'max' => 255]
        ];
    }

    public static function find() {
       return new MediaQuery(get_called_class());
    }

    public function attributeLabels() {
        return [
        'Id' => 'ID',
        'Name' => 'Name',
        'URI' => 'Uri',
        'Width' => 'Width',
        'Height' => 'Height',
        'Extra' => 'Extra',
        ];
    }
}

class MediaQuery extends ActiveQuery {

    public function forItem($item, $attribute = null) {

        if (is_object($item))
            $item = $item->getDocumentId()->getId();

        if ($attribute != null)
            $item .= "::" . $attribute;

        $this->andWhere(['For' => $item]);
        return $this;
    }

    // public function one($db = null) {
    //     return $this->cachedOne($db);
    // }
}
