<?php

class CommentController extends Zend_Controller_Action
{
  
  public function init()
  {
    $this->params = $this->getRequest()->getParams();
  }

  public function indexAction()
  {
    // print_r($this->params);
    $content = htmlentities($this->params['content'], ENT_COMPAT, "UTF-8");
    $author = htmlentities('~' . $this->params['author'], ENT_COMPAT, "UTF-8");
    $authorIp = $_SERVER['REMOTE_ADDR'];
    $objectId = $this->params['com_object_id'];
    $objectType = $this->params['com_object_type'];

    switch ($this->params['com_object_type']) {
      case 'a':
        $data = Model_Album_Api::getInstance()->redirectById($this->params['com_object_id']);
        $redirect = Jkl_Tools_Url::createUrl($data['art_name'] . '+-+' . $data['alb_title'] . '-a' . $data['alb_id'] . '.html');
        break;
        
      case 'p':
      case 'wykonawca':
        $data = Model_Artist_Api::getInstance()->redirectById($this->params['com_object_id']);
        $redirect = Jkl_Tools_Url::createUrl($data['art_name'] . '-p' . $data['art_id'] . '.html');
        break;
      
      case 'l':
      case 'wytwornia':
        $data = Model_Label_Api::getInstance()->redirectById($this->params['com_object_id']);
        $redirect = Jkl_Tools_Url::createUrl($data['lab_name'] . '-l' . $data['lab_id'] . '.html');
        break;
      
      case 's':
      case 'utwor':
        $data = Model_Song_Api::getInstance()->redirectById($this->params['com_object_id']);
        $redirect = Jkl_Tools_Url::createUrl($data['sng_title'] . '-s' . $data['sng_id'] . '.html');
        break;
        
      case 'n':  
      case 'news':
        $data = Model_News_Api::getInstance()->redirectById($this->params['com_object_id']);
        $redirect = Jkl_Tools_Url::createUrl($data['nws_title'] . '-n' . $data['nws_id'] . '.html');
        break;
  
      default:
        break;
    }
    
    if (empty($content)) {
      $redirect .= '?postError=1&emptyComment=1';
      header("Location: /" . str_replace(' ', '+', $redirect));
      exit();
    }
    
    if (strlen($content) > 1000) {
      $redirect .= '?postError=1&commentTooLong=1';
      header("Location: /" . str_replace(' ', '+', $redirect));
      exit();
    }
        
    $result = Model_Comment_Api::getInstance()->postComment($content, $author, $authorIp, $objectId, $objectType);
    
    if ($data) {
      // data validation      
      if (!$result) {
        $redirect .= '?postError=1&dbError=1';
      }
      header("Location: /" . str_replace(' ', '+', $redirect . '#comments'));
      exit();
    }
    else {
      header("Location: /404.html", true, 404);
      exit();
    }
  }
  
  public function viewAction()
  {
  }
}