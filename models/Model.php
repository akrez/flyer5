<?php

namespace app\models;

use yii\base\Model as BaseModel;

class Model extends BaseModel
{

    public function attributeLabels()
    {
        return static::attributeLabelsList();
    }

    public static function attributeLabelsList()
    {
        return [
            'id' => 'شناسه',
            'updatedAt' => 'تاریخ ویرایش',
            'createdAt' => 'تاریخ ایجاد',
            'status' => 'وضعیت',
            'email' => 'ایمیل',
            'passwordHash' => 'پسورد',
            'token' => 'Token',
            'password' => 'پسورد',
            'resetToken' => 'Reset Token',
            'resetAt' => 'Reset At',
            'mobile' => 'موبایل',
            //
            'fullname' => 'نام',
            'fatherName' => 'نام پدر',
            'code' => 'کد',
            'mobile' => 'موبایل',
            'gender' => 'جنسیت',
            'nationalCode' => 'کدملی',
            'birthdate' => 'تاریخ تولد',
            'role' => 'نقش',
            'des' => 'توضیحات',
            //
            'name' => 'نام',
            'shortname' => 'نام اختصاری',
            'unit' => 'واحد',
            'categoryId' => 'دسته‌بندی',
            'parentId' => 'دسته‌بندی',
            //
            'oldValue' => 'مقدار قبلی',
            'newValue' => 'مقدار جدید',
            //
            'entityAttribute' => 'ویژگی',
            //
            'qty' => 'مقدار',
            'rawId' => 'ماده خام',
            'place' => 'مکان',
            'count' => 'تعداد',
            //
            'price' => 'قیمت',
            'factor' => 'فاکتور',
            'sellerId' => 'فروشنده',
            'submitAt' => 'تاریخ ثبت',
            'factorAt' => 'تاریخ فاکتور',
            'providerId' => 'سازنده',
            //
            'qc' => 'Qc',
            'qa' => 'Qa',
            'productAt' => 'تاریخ تولید',
            'parentBarcode' => 'والد',
            'typeId' => 'نوع',
            //
            'entityId' => 'موجودیت',
            '_count' => 'تعداد سری',
            'barcode' => 'بارکد',
        ];
    }
}
