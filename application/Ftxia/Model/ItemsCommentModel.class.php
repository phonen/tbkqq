<?php
namespace Ftxia\Model;
use Common\Model\CommonModel;
class ItemsCommentModel extends  CommonModel {
     protected $_auto = array (
    	array('last_time','time',1,'function'),
    	array('add_time','time',1,'function'),
    );
}