<?php

PruneMazui\DdlGenerator\TableDefinition\TableDefinition::__set_state(array(
   'schemas' => 
  array (
    '' => 
    PruneMazui\DdlGenerator\TableDefinition\Schema::__set_state(array(
       'schema_name' => '',
       'tables' => 
      array (
        't_user' => 
        PruneMazui\DdlGenerator\TableDefinition\Table::__set_state(array(
           'tableName' => 't_user',
           'comment' => 'ユーザー',
           'columns' => 
          array (
            'user_id' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'user_id',
               'dataType' => 'SMALLINT',
               'required' => true,
               'length' => NULL,
               'default' => NULL,
               'comment' => 'ユーザー',
               'isAutoIncrement' => true,
            )),
            'client_id' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'client_id',
               'dataType' => 'CHAR',
               'required' => true,
               'length' => 40,
               'default' => NULL,
               'comment' => 'クライアントID',
               'isAutoIncrement' => false,
            )),
            'ip_addr' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'ip_addr',
               'dataType' => 'VARCHAR',
               'required' => true,
               'length' => 128,
               'default' => NULL,
               'comment' => 'IPアドレス',
               'isAutoIncrement' => false,
            )),
            'insert_date' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'insert_date',
               'dataType' => 'TIMESTAMP',
               'required' => false,
               'length' => NULL,
               'default' => NULL,
               'comment' => '登録日時',
               'isAutoIncrement' => false,
            )),
            'update_date' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'update_date',
               'dataType' => 'TIMESTAMP',
               'required' => false,
               'length' => NULL,
               'default' => NULL,
               'comment' => '更新日時',
               'isAutoIncrement' => false,
            )),
          ),
           'primary_key' => 
          array (
            0 => 'user_id',
          ),
        )),
        't_image' => 
        PruneMazui\DdlGenerator\TableDefinition\Table::__set_state(array(
           'tableName' => 't_image',
           'comment' => '画像',
           'columns' => 
          array (
            'image_id' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'image_id',
               'dataType' => 'INTEGER',
               'required' => true,
               'length' => NULL,
               'default' => NULL,
               'comment' => '画像連番',
               'isAutoIncrement' => true,
            )),
            'access_key' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'access_key',
               'dataType' => 'CHAR',
               'required' => true,
               'length' => 40,
               'default' => NULL,
               'comment' => 'アクセスキー',
               'isAutoIncrement' => false,
            )),
            'size' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'size',
               'dataType' => 'INTEGER',
               'required' => true,
               'length' => NULL,
               'default' => NULL,
               'comment' => 'サイズ',
               'isAutoIncrement' => false,
            )),
            'width' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'width',
               'dataType' => 'SMALLINT',
               'required' => true,
               'length' => NULL,
               'default' => NULL,
               'comment' => '横幅',
               'isAutoIncrement' => false,
            )),
            'height' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'height',
               'dataType' => 'SMALLINT',
               'required' => true,
               'length' => NULL,
               'default' => NULL,
               'comment' => '高さ',
               'isAutoIncrement' => false,
            )),
            'user_id' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'user_id',
               'dataType' => 'SMALLINT',
               'required' => true,
               'length' => NULL,
               'default' => NULL,
               'comment' => 'ユーザーID',
               'isAutoIncrement' => false,
            )),
            'insert_date' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'insert_date',
               'dataType' => 'TIMESTAMP',
               'required' => false,
               'length' => NULL,
               'default' => NULL,
               'comment' => '登録日時',
               'isAutoIncrement' => false,
            )),
          ),
           'primary_key' => 
          array (
            0 => 'image_id',
          ),
        )),
        't_image_data' => 
        PruneMazui\DdlGenerator\TableDefinition\Table::__set_state(array(
           'tableName' => 't_image_data',
           'comment' => '画像データ',
           'columns' => 
          array (
            'image_id' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'image_id',
               'dataType' => 'INTEGER',
               'required' => true,
               'length' => NULL,
               'default' => NULL,
               'comment' => '画像連番',
               'isAutoIncrement' => false,
            )),
            'data' => 
            PruneMazui\DdlGenerator\TableDefinition\Column::__set_state(array(
               'columnName' => 'data',
               'dataType' => 'LONGBLOB',
               'required' => true,
               'length' => NULL,
               'default' => NULL,
               'comment' => '画像バイナリ',
               'isAutoIncrement' => false,
            )),
          ),
           'primary_key' => 
          array (
            0 => 'image_id',
          ),
        )),
      ),
    )),
  ),
));