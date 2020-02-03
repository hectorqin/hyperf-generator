<?php

return [
    // 要生成的表名
    'table'  => null,

    // 要排除的表名
    'except' => [],

    // 要生成的类型
    // 'type'   => ['m', 'v', 'c', 'p', 's'],
    'type'   => [],

    // 是否 dryRun
    'dryRun' => false,

    // 是否覆盖已有文件
    'force'   => false,

    // 数据库配置
    'dbConfigName' => 'database',
    // 数据库名称
    'databaseName' => '',
    // 模型 表的数据库前缀
    'dbNamePrefix' => '',

    // 表前缀，不参与文件命名和类命名
    'tablePrefix'  => [],

    // 忽略的字段名
    'ignoreFields' => [],

    // 字段类型映射
    'varcharFieldMap'   => ['varchar', 'char', 'text', 'mediumtext'],
    'enumFieldMap'      => ['tinyint'],
    'timestampFieldMap' => ['date', 'datetime'],
    'numberFieldMap'    => ['int'],

    'idFieldMap' => ['tinyint', 'smallint', 'mediumint', 'int', 'bigint'],

    // 字段类型匹配
    'createFieldMap' => ['createtime', 'create_time', 'createdtime', 'created_time', 'createat', 'create_at', 'createdat', 'created_at'],
    'updateFieldMap' => ['updatetime', 'update_time', 'updatedtime', 'updated_time', 'updateat', 'update_at', 'updatedat', 'updated_at'],
    'deleteFieldMap' => ['deletetime', 'delete_time', 'deletedtime', 'deleted_time', 'deleteat', 'delete_at', 'deletedat', 'deleted_at'],
    'passwordFieldMap' => ['password', 'pwd', 'encrypt'],

    'intFieldTypeList' => ['tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'boolean', 'serial'],
    'floatFieldTypeList' => ['decimal', 'float', 'double', 'real'],

    // 自定义模板路径
    'templateDir' => '',

    // 模型模块
    'mModule' => 'common',
    // 校验器模块
    'vModule' => 'common',
    // 控制器模块
    'cModule' => 'index',

    // 模型分层
    'mLayer' => '',
    // 校验器分层
    'vLayer' => '',
    // 控制器分层
    'cLayer' => '',

    // 模型继承类
    'mBase'   => 'think\\Model',
    // 校验器继承类
    'vBase'   => 'think\\Validate',
    // 控制器继承类
    'cBase'   => 'think\\Controller',

    // 模型后缀
    'mSuffix' => '',
    // 校验器后缀
    'vSuffix' => '',
    // 控制器后缀
    'cSuffix' => '',

    // 模型继承类名
    'mBaseName' => 'Model',
    // 校验器继承类名
    'vBaseName' => 'Validate',
    // 控制器继承类名
    'cBaseName' => 'Controller',

    // postman 项目名
    'projectName' => '',
    // postman api前缀
    'postmanAPIHost' => '{{api_prefix}}',
];