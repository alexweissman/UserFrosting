<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

/**
 * Modern Standard Arabic message token translations for the 'admin' sprinkle.
 *
 * @author Alexander Weissman and Abdullah Seba
 */
return [
  'ACTIVITY' => [
    1      => 'نشاط',
    2      => 'أنشطة',
    'LAST' => 'النشاط الاخير',
    'PAGE' => 'قائمة من أنشطة المستخدم',
    'TIME' => 'وقت نشاط',
  ],
  'CACHE' => [
    'CLEAR'             => 'مسح ذاكرة التخزين',
    'CLEAR_CONFIRM'     => 'هل أنت متأكد أنك تريد مسح ذاكرة التخزين بالموقع؟',
    'CLEAR_CONFIRM_YES' => 'نعم، إمسح ذاكرة التخزين',
    'CLEARED'           => 'تم مسح ذاكرة التخزين بنجاح',
  ],
  'DASHBOARD'           => 'لوحة القيادة',
  'NO_FEATURES_YET'     => 'لا يبدو أن أي ميزات تم إعدادها لهذا الحساب حتى الآن. ربما لم يتم تنفيذها بعد، أو ربما شخص نسي أن يعطيك الوصول. في كلتا الحالتين، نحن سعداء أن يكون لك على متن!',
  'DELETE_MASTER'       => 'لا يمكنك حذف الحساب الرئيسي',
  'DELETION_SUCCESSFUL' => 'المستعمل <strong>{{user_name}}</strong> حذف بنجاح',
  'DETAILS_UPDATED'     => 'جدد تفاصيل الحساب للمستخدم <strong>{{user_name}}</strong>',
  'DISABLE_MASTER'      => 'لا يمكنك تعطيل الحساب الرئيسي',
  'DISABLE_SELF'        => 'You cannot disable your own account!',
  'DISABLE_SUCCESSFUL'  => 'حساب المستخدم <strong>{{user_name}}</strong> عطيل بنجاح',
  'ENABLE_SUCCESSFUL'   => 'حساب المستخدم <strong>{{user_name}}</strong> مكين بنجاح',
  'GROUP'               => [
    1                     => 'مجموعة',
    2                     => 'مجموعات',
    'CREATE'              => 'إنشاء مجموعة',
    'CREATION_SUCCESSFUL' => 'Successfully created group <strong>{{name}}</strong>',
    'DELETE'              => 'حذف مجموعة',
    'DELETE_CONFIRM'      => 'هل أنت متأكد أنك تريد حذف مجموعة <strong>{{name}}</strong>?',
    'DELETE_DEFAULT'      => 'You can\'t delete the group <strong>{{name}}</strong> because it is the default group for newly registered users.',
    'DELETE_YES'          => 'نعم، إحذف مجموعة',
    'DELETION_SUCCESSFUL' => 'Successfully deleted group <strong>{{name}}</strong>',
    'EDIT'                => 'تعديل مجموعة',
    'ICON'                => 'رمز المجموعة',
    'ICON_EXPLAIN'        => 'رمز المستخدمين في المجموعه',
    'INFO_PAGE'           => 'صفحة معلومات المجموعة ل {{name}}',
    'MANAGE'              => 'Manage group',
    'NAME'                => 'أسم المجموعة',
    'NAME_EXPLAIN'        => 'ادخال اسم للمجموعة',
    'NOT_EMPTY'           => 'You can\'t do that because there are still users associated with the group <strong>{{name}}</strong>.',
    'PAGE_DESCRIPTION'    => 'قائمة المجموعات لموقعك يوفر أدوات لإدارة التحرير وحذف مجموعات',
    'SUMMARY'             => 'Group Summary',
    'UPDATE'              => 'Details updated for group <strong>{{name}}</strong>',
  ],
  'MANUALLY_ACTIVATED'    => 'تم تفعيل حساب{{user_name}}',
  'MASTER_ACCOUNT_EXISTS' => 'الحساب الرئيسي موجود بالفعل',
  'MIGRATION'             => [
    'REQUIRED' => 'تحديث قاعدة البيانات مطلوب',
  ],
  'PERMISSION' => [
    1                  => 'الإذن',
    2                  => 'مأذونيات',
    'ASSIGN_NEW'       => 'تعيين إذن جديد',
    'HOOK_CONDITION'   => 'الظروف',
    'ID'               => 'Permission ID',
    'INFO_PAGE'        => 'Permission information page for \'{{name}}\'',
    'MANAGE'           => 'إدارة المأذونات',
    'NOTE_READ_ONLY'   => '<strong>Please note:</strong> permissions are considered "part of the code" and cannot be modified through the interface.  To add, remove, or modify permissions, the site maintainers will need to use a <a href="https://learn.userfrosting.com/database/extending-the-database" target="about:_blank">database migration.</a>',
    'PAGE_DESCRIPTION' => 'قائمة المأذونات لموقعك',
    'SUMMARY'          => 'Permission Summary',
    'UPDATE'           => 'تحديث المأذونات',
    'VIA_ROLES'        => 'Has permission via roles',
  ],
  'ROLE' => [
    1                     => 'وظيفة',
    2                     => 'وظائف',
    'ASSIGN_NEW'          => 'تعيين دور جديد',
    'CREATE'              => 'إنشاء دور',
    'CREATION_SUCCESSFUL' => 'Successfully created role <strong>{{name}}</strong>',
    'DELETE'              => 'حذف دور',
    'DELETE_CONFIRM'      => 'هل أنت متأكد أنك تريد حذف الدور <strong>{{name}}</strong>?',
    'DELETE_DEFAULT'      => 'You can\'t delete the role <strong>{{name}}</strong> because it is a default role for newly registered users.',
    'DELETE_YES'          => 'نعم، حذف دور',
    'DELETION_SUCCESSFUL' => 'Successfully deleted role <strong>{{name}}</strong>',
    'EDIT'                => 'إدارة دور',
    'HAS_USERS'           => 'You can\'t do that because there are still users who have the role <strong>{{name}}</strong>.',
    'INFO_PAGE'           => 'صفحة معلومات دور {{name}}',
    'MANAGE'              => 'إدارة الوظائف',
    'NAME'                => 'اسم',
    'NAME_EXPLAIN'        => 'أدخل اسما للدور',
    'NAME_IN_USE'         => 'A role named <strong>{{name}}</strong> already exist',
    'PAGE_DESCRIPTION'    => 'قائمة الوظائف لموقعك',
    'PERMISSIONS_UPDATED' => 'Permissions updated for role <strong>{{name}}</strong>',
    'SUMMARY'             => 'Role Summary',
    'UPDATED'             => 'تحديث الوظائف',
  ],
  'SYSTEM_INFO' => [
    '@TRANSLATION' => 'معلومات الجهاز',
    'DB_NAME'      => 'اسم قاعدة البيانات',
    'DB_VERSION'   => 'إصدار قاعدة البيانات',
    'DIRECTORY'    => 'دليل المشروع',
    'PHP_VERSION'  => 'الإصدار PHP',
    'SERVER'       => 'برنامج الخادم',
    'SPRINKLES'    => 'sprinkles المحمل',
    'UF_VERSION'   => 'إصدار UserFrosting',
    'URL'          => 'رابط قاعدة الموقع',
  ],
  'TOGGLE_COLUMNS' => 'Toggle columns',
  'USER'           => [
    1       => 'مستخدم',
    2       => 'المستخدمين',
    'ADMIN' => [
      'CHANGE_PASSWORD'    => 'تغيير كلمة المرور للمستخدم',
      'SEND_PASSWORD_LINK' => 'إرسال المستخدم وصلة من شأنها أن تسمح لهم لاختيار كلمة المرور الخاصة بهم',
      'SET_PASSWORD'       => 'تعيين كلمة المرور الخاصة بالمستخدم',
    ],
    'ACTIVATE'         => 'تفعيل المستخدم',
    'CREATE'           => 'إنشاء مستخدم',
    'CREATED'          => 'User <strong>{{user_name}}</strong> has been successfully created',
    'DELETE'           => 'مسح المستخدم',
    'DELETE_CONFIRM'   => 'هل أنت متأكد أنك تريد حذف المستخدم <strong>{{name}}</strong>?',
    'DELETE_YES'       => 'نعم، حذف المستخدم',
    'DELETED'          => 'User deleted',
    'DISABLE'          => 'تعطيل المستخدم ',
    'EDIT'             => 'إدارة المستخدم',
    'ENABLE'           => 'تمكين المستخدم',
    'INFO_PAGE'        => 'صفحة معلومات المستخدم {{name}}',
    'LATEST'           => 'أحدث المستخدمين',
    'PAGE_DESCRIPTION' => 'قائمة المستخدمين لموقعك',
    'SUMMARY'          => 'Account Summary',
    'VIEW_ALL'         => 'عرض جميع المستخدمين',
    'WITH_PERMISSION'  => 'Users with this permission',
  ],
  'X_USER' => [
    0 => 'لا يوجد اي مستخدمين',
    1 => '{{plural}} مستخدم',
    2 => '{{plural}} المستخدمين',
  ],
];
