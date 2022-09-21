<?php

namespace app\models\queries;

/**
 * This is the ActiveQuery class for [[Cover]].
 *
 * @see Cover
 */
class CoverQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Cover[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Cover|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
