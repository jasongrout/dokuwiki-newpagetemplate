<?php
/**
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Jason grout <jason-doku@creativetrax.com>>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_newpagetemplate extends DokuWiki_Action_Plugin {

  /**
   * return some info
   */
  function getInfo(){
    return array(
      'author' => 'Jason Grout',
      'email'  => 'jason-doku@creativetrax.com',
      'date'   => '2007-02-24',
      'name'   => 'newpagetemplate',
      'desc'   => 'Loads into the new page creation box a template specified in the $_REQUEST "newpagetemplate" parameter (i.e., can be passed in the URL or as a form value).',
      'url'    => '',
    );
  }

  /**
   * register the eventhandlers
   */
  function register(&$contr){
    $contr->register_hook('HTML_PAGE_FROMTEMPLATE', 'BEFORE', $this, 'pagefromtemplate', array());
  }

  function pagefromtemplate(&$event, $param) {  
     if(strlen(trim($_REQUEST['newpagetemplate']))>0) {
       global $conf;
       global $INFO;
       
       $tpl = io_readFile(wikiFN($_REQUEST['newpagetemplate']));
       
       if($this->getConf('userreplace')) {
	 
	 $stringvars=array_map(create_function('$v', 'return explode(",",$v,2);'),
			       explode(';',$_REQUEST['newpagevars']));
	 foreach($stringvars as $value) {
	   $tpl = str_replace(trim($value[0]),trim($value[1]),$tpl);
	 }
       }
       
       if($this->getConf('standardreplace')) {
	 $tpl = str_replace('@ID@',$id,$tpl);
	 $tpl = str_replace('@NS@',getNS($id),$tpl);
	 $tpl = str_replace('@PAGE@',strtr(noNS($id),'_',' '),$tpl);
	 $tpl = str_replace('@USER@',$_SERVER['REMOTE_USER'],$tpl);
	 $tpl = str_replace('@NAME@',$INFO['userinfo']['name'],$tpl);
	 $tpl = str_replace('@MAIL@',$INFO['userinfo']['mail'],$tpl);
	 $tpl = str_replace('@DATE@',date($conf['dformat']),$tpl);
       }
       $event->result=$tpl;
       $event->preventDefault(); 
     }
  }
  
}

//Setup VIM: ex: et ts=4 enc=utf-8 :
