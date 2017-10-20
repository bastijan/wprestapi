<?php
//data version
class Logic
{
 	private $param;
 	private $db;
	private $route;
    private $memcache;

	function __construct()
	{
		global $conf;
		$this->db=dbClass::getInstance($conf['dbhost'],$conf['dbuser'],$conf['dbpass'],$conf['dbname']);
		$this->route=new Route();
		$this->param=new Params();

    $this->memcached = new Memcached;
    $this->memcached->addServer('localhost',11211);

	}

 	function info( $args = false ) {
        phpinfo();
    }


/*================================================================================
*  rutiranje i ostalo
================================================================================*/
	function func_not_found( $args = false ) {
		echo '404';

	}

	function ws( $args = false ) {
		global $conf;
		$json = array("status" => 1,
      "msg" => "FH WebService",
      "NOTE:"=>"all params started with ':' are optional",
      "Freizeit Heroes"=>"Only for Freizeit heroes",
      "------------------------"=>"---------------------",
			"data/vendor/usermeta/:id"  	=> "Get vendor's usermeta data",
      "data/vendor/customers/:id"   => "Get all vendor's customers",
      "data/users"                  => "All users data, short format",
      "data/user/:id"               => "One user daya",
      "data/users/usermeta"         => "Users data, all data, with billing and shipping data",
      "data/users/usermeta/:id"     => "Users data (user=:id), all data, with billing and shipping data",
      "data/gen/:table/:id"  	=> "GET, POST and PUT data from/to 'table' on data 'id'",
      "data/postmeta/:post_id"  	=> "GET, POST and PUT data from/to 'wp_postmeta' on data 'post_id'",
      "==========================="=>"=================================",
      "WordPress"=>"WordPress posts GET API's",
      "............................."=>"...............................",
      "data/latest/:limit/:page"  	=> "The latest posts",
      "data/popular/:limit/:page"=>"The most popular posts",
      "data/favorite"  => "The favorite posts",
			"data/post/:id"		=> "Get WP post (page,order, image,attachment, etc.) by ID",
			"data/category/top"=> "Top categories",
			"data/total/category/:id"=>"Get total number subcategoriest that have category :id",
			"data/total/category/posts/:id"=>"Get total number of posts by category :id",
			"data/category/:id/:limit/:page"=>"Get 'limit' posts from category 'id', page=1 for the first page; also return total pages number",
      "data/subcategory/:id"=>"get subcategories for category :id",
      "data/slider"=> "Slider images url",
      "data/:url"=>"Get post by their url",
      "data/topmenu"=>"Top menu",
      "data/menu"=>"Main menu",
      "data/footer/menu"=>"Footer menu",
      "data/catbyname/:name/:limit/:page"=>"Get posts for category addressed by category name",
      "data/subcatbyparent/:name/:limit/:page"=> "Get posts for category addressed by parent category name",
      "data/allposts/:limit/:page"=>"All posts except favorites",
      "data/related/:id/:limit"=>"Posts related by post :id",
      "data/pages/:id/:limit"=>"Get all pages",
      
      );


		/* Output header */
		//header('Content-type: application/vnd.api+json');
		echo json_encode($json);

    }

    function getVendorUsermeta($args = false) {
      global $conf;

      if (isset($args['id'])) {
        $queryArray = $this->db->queryHandler->getQuery("getVendorUsermeta");
      }      
      else {
        $queryArray = $this->db->queryHandler->getQuery("getVendorUsermetaById");
        $args['id']=false;
      }
 		  
  		$dataArray = $this->executeSQL($queryArray,$args['id']);

      $data=array();
      $i=-1; $user_id=0;
      foreach ($dataArray as $k=>$v) {
        if ($v['user_id']!=$user_id) {
          $i++;
          $data[$i]['user_id']=$v['user_id'];
          $user_id=$v['user_id'];
          $queryArrayU = $this->db->queryHandler->getQuery("getUserByid");
          $dataArrayU = $this->executeSQL($queryArrayU,$user_id);
          $data[$i]['user_nicename']=$dataArrayU[0]['user_nicename'];
          $data[$i]['user_email']=$dataArrayU[0]['user_email'];
          $data[$i]['user_url']=$dataArrayU[0]['user_url'];
          $data[$i]['gravatar']=$this->get_gravatar($dataArrayU[0]['user_email'], $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ); 
        }      
        $data[$i][$v['meta_key']]=$v['meta_value'];
      }

  		echo json_encode($data);      
    }

    function getUsersUsermeta($args = false) {
      global $conf;

      if (isset($args['id'])) {
        $queryArray = $this->db->queryHandler->getQuery("getUserUsermetaById");
      }      
      else {
        $queryArray = $this->db->queryHandler->getQuery("getUsersUsermeta");
        $args['id']=false;
      }
 		  
  		$dataArray = $this->executeSQL($queryArray,$args['id']);

      $data=array();
      $i=-1; $user_id=0;
      foreach ($dataArray as $k=>$v) {
        if ($v['user_id']!=$user_id) {
          $i++;
          $data[$i]['user_id']=$v['user_id'];
          $user_id=$v['user_id'];
          $queryArrayU = $this->db->queryHandler->getQuery("getUserByid");
          $dataArrayU = $this->executeSQL($queryArrayU,$user_id);
          $data[$i]['username']=$dataArrayU[0]['user_login'];
          $data[$i]['email']=$dataArrayU[0]['user_email'];
          $data[$i]['user_url']=$dataArrayU[0]['user_url'];
          $data[$i]['gravatar']=$this->get_gravatar($dataArrayU[0]['user_email'], $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ); 
        }     
        if ($v['meta_key']=='first_name' || $v['meta_key']=='last_name') {
          $data[$i][$v['meta_key']]=$v['meta_value']; 
        }        

        if (substr($v['meta_key'],0,8)=='billing_') {
          $data[$i]['billing'][substr($v['meta_key'],8)]=$v['meta_value'];
        }
        elseif (substr($v['meta_key'],0,9)=='shipping_') {
          $data[$i]['shipping'][substr($v['meta_key'],9)]=$v['meta_value'];
        }
        
      }

  		echo json_encode($data);      
    }    

    function getUsersShort($args = false) {
      global $conf;
      $queryArray = $this->db->queryHandler->getQuery("getUsers");   
      $dataArray = $this->executeSQL($queryArray);   
      echo json_encode($dataArray); 
    }



    function getUserById($args = false) {
      global $conf;
      if (!isset($args['id'])) {
        echo 'ERROR: Missing user_id';   
        exit;
      }
      $queryArray = $this->db->queryHandler->getQuery("getUserByid");   
      $dataArray = $this->executeSQL($queryArray,$args['id']);   
      echo json_encode($dataArray); 
    }

    
    function getVendorCustomersByVendorId ($args = false) {
      global $conf;
      $queryArray = $this->db->queryHandler->getQuery("getVendorCustomersByVendorId");   
      $dataArray = $this->executeSQL($queryArray,$args['id']); 

      $data=array();
      $i=-1; $user_id=0;
      foreach ($dataArray as $k=>$v) {
       // if ($v['ID']!=$user_id) {
          $i++;
          $data[$i]['user_id']=$v['ID'];
          $user_id=$v['ID'];

          $data[$i]['username']=$dataArrayU[$k]['user_login'];
          $data[$i]['email']=$dataArrayU[$k]['user_email'];
          $data[$i]['user_url']=$dataArrayU[$k]['user_url'];
          $data[$i]['gravatar']=$this->get_gravatar($dataArrayU[$k]['user_email'], $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ); 
       // }     

        $queryArrayUM = $this->db->queryHandler->getQuery("getUserUsermetaById");
        $dataArrayUM = $this->executeSQL($queryArrayUM,$user_id);

        foreach ($dataArrayUM as $km=>$vm) {
          if ($vm['meta_key']=='first_name' || $vm['meta_key']=='last_name') {
            $data[$i][$vm['meta_key']]=$vm['meta_value']; 
          }        

          if (substr($vm['meta_key'],0,8)=='billing_') {
            $data[$i]['billing'][substr($vm['meta_key'],8)]=$vm['meta_value'];
          }
          elseif (substr($vm['meta_key'],0,9)=='shipping_') {
            $data[$i]['shipping'][substr($vm['meta_key'],9)]=$vm['meta_value'];
          }   
        }

     
      } 
      echo json_encode($data); 
    }

    function getLatestPosts($args = false) {
  		global $conf;
      $page=false;
      if (!isset($args['limit'])) {
        $limit=10;
      }
      else {
        $limit=$args['limit'];
      }

      if (isset($args['page'])) {
        if (intval($args['page'])>1) {
          $page=($args['page']-1)*$limit+1;
        }
        else {
          //$page=(int)$args['page'];
          $page=false;
        }
      }

  		$queryArray = $this->db->queryHandler->getQuery("getLatestPosts");

  		$dataArray = $this->executeSQL($queryArray,false,$limit,$page);

  		$dataArray = $this->clearHtml($dataArray);
  		/** header('Content-type: application/json'); */
  		echo json_encode($dataArray);
    }

    function getFavoritePosts($args = false) {
  		global $conf;

  		$queryArray = $this->db->queryHandler->getQuery("getFavoritePosts");

  		$dataArray = $this->executeSQL($queryArray);
  		 $dataArray = $this->clearHtml($dataArray);
  		/** header('Content-type: application/json'); */
  		echo json_encode($dataArray);
    }

    function getMostPopularPosts($args = false) {
      global $conf;
      $page=1;
      if (!isset($args['limit'])) {
        $limit=10;
      }
      else {
        $limit=$args['limit'];
      }

      if (isset($args['page'])) {
        if (intval($args['page'])>1) {
          $page=$args['page']*$limit+1;
        }
        else {
          $page=(int)$args['page'];
        }
      }

      $queryArray = $this->db->queryHandler->getQuery("getMostPopularPosts");

      $dataArray = $this->executeSQL($queryArray,false,$limit,$page);
    //  echo '<pre>';
    //  var_dump($dataArray);

      $dataArray = $this->clearHtml($dataArray);
      /** header('Content-type: application/json'); */
      echo json_encode($dataArray);

    }



    function getFavoriteNewsAndImage() {
      global $conf;

      $queryArray = $this->db->queryHandler->getQuery("getFavoriteNewsAndImage");

      $dataArray = $this->executeSQL($queryArray);
      $dataArray = $this->clearSerHtml($dataArray);

      /** header('Content-type: application/json'); */
      echo json_encode($dataArray);
    }


    function getPost($args=false) {
      global $conf;

      $queryArray = $this->db->queryHandler->getQuery("getPost");

      $dataArray = $this->executeSQL($queryArray,$args['url']);
      $dataArray[0]['vest']=nl2br($dataArray[0]['vest']);

      $dataArray=$this->extractImage($dataArray);

      /** header('Content-type: application/json'); */
      echo json_encode($dataArray);

    }

    function getPostByID($args = false) {
		global $conf;

		$queryArray = $this->db->queryHandler->getQuery("getPostByID");
		//$queryArray = $this->db->queryHandler->getQuery("getLatestPostsView10");

		$dataArray = $this->executeSQL($queryArray,intval($args['id']));
    $dataArray[0]['vest']=nl2br($dataArray[0]['vest']);
		/** header('Content-type: application/json'); */
		echo json_encode($dataArray);
    }

    function getPostsWOutIzdvajamo($args = false) {
        global $conf;
		if (!isset($args['limit'])) {
			$limit=15;
		}
		else {
			$limit=$args['limit'];
		}
		if (isset($args['page'])) {
		 	if (intval($args['page'])>1) {
				$page=$args['page']*$limit+1;
			}
			else {
				$page=$args['page'];
			}
		}
		else {
			$page=1;
		}
        $queryArray = $this->db->queryHandler->getQuery("getPostsWOutIzdvajamo");

	    //$queryArray = $this->db->queryHandler->getQuery("getLatestPostsView10");
	    $dataArray = $this->executeSQL($queryArray,false,$limit,$page);

		$dataArray = $this->clearHtml($dataArray);

	    /** header('Content-type: application/json'); */
	    echo json_encode($dataArray);
    }

    function getTopMenu($args = false) {
		global $conf;

		$queryArray = $this->db->queryHandler->getQuery("getTopMenu");
		//$queryArray = $this->db->queryHandler->getQuery("getLatestPostsView10");

		$dataArray = $this->executeSQL($queryArray);
		/** header('Content-type: application/json'); */
		echo json_encode($dataArray);
    }



    function getTopCategoryList($args = false) {
		global $conf;

		$queryArray = $this->db->queryHandler->getQuery("getTopCategoryList");
		//$queryArray = $this->db->queryHandler->getQuery("getLatestPostsView10");

		$dataArray = $this->executeSQL($queryArray);
		/** header('Content-type: application/json'); */
		echo json_encode($dataArray);
    }

    function getSumOfPostInCatID($args = false) {
		global $conf;

		$queryArray = $this->db->queryHandler->getQuery("getSumOfPostInCatID");
		//$queryArray = $this->db->queryHandler->getQuery("getLatestPostsView10");

		$dataArray = $this->executeSQL($queryArray,$args['id']);
		/** header('Content-type: application/json'); */
		echo json_encode($dataArray);
    }

    function getPostsByCatID($args = false) {
  		global $conf;
  		if (!isset($args['limit'])) {
  			$limit=15;
  		}
  		else {
  			$limit=$args['limit'];
  		}
  		if (isset($args['page'])) {
  		 	if (intval($args['page'])>1) {
  				$page=$args['page']*$limit+1;
  			}
  			else {
  				$page=$args['page'];
  			}
  		}
  		else {
  			$page=1;
  		}
  		$queryArray = $this->db->queryHandler->getQuery("getPostsByCatID");
  		//$queryArray = $this->db->queryHandler->getQuery("getLatestPostsView10");


  		$dataArray = $this->executeSQL($queryArray,$args['id'],$limit,$page);

  		$dataArray = $this->clearHtml($dataArray);

  		header('Content-type: application/json',JSON_UNESCAPED_UNICODE);
  		echo json_encode($dataArray);
    }


    function getPostsByParentName($args = false) {
        global $conf;
        if (!isset($args['limit'])) {
          $limit=4;
        }
        else {
          $limit=$args['limit'];
        }
        if (isset($args['page'])) {
          if (intval($args['page'])>1) {
            $page=$args['page']*$limit+1;
          }
          else {
            $page=$args['page'];
          }
        }
        else {
          $page=0;
        }

       //$key=md5('top_menu3');
        global $conf;
        $menu=array();


          $arrSQL = $this->db->queryHandler->getQuery("getCatByName");
          $arrCat = $this->executeSQL($arrSQL,$args['name']);

          $arrSQLSubCat = $this->db->queryHandler->getQuery("getSubCategoryListForParentID");
          $arrSubCat = $this->executeSQL($arrSQLSubCat,$arrCat[0]['cat_id']);

          foreach ($arrSubCat as $k2 => $v2) {

            $arrSQLPosts = $this->db->queryHandler->getQuery("getPostsByCatIDwImage");
            $arrPosts = $this->executeSQL($arrSQLPosts,$arrSubCat[0]['term_id'],$limit,$page);

            foreach ($arrPosts as $k3 => $v3) {
              $arrSubCat[$k2]['article'][$k3]=array(
              'thumb'=>$v3['meta_value'],
              'url'=>$v3['guid'],
              'title'=>$v3['post_title'],
              'post_name'=>$v3['post_name']
              );
            }
          }


      //echo '<pre>';
      //print_r($menu);
      //$this->memcache->set($key, $menu, MEMCACHE_COMPRESSED, 150);
      /** header('Content-type: application/json'); */
      $ret=$this->array2json($arrSubCat);
      echo $ret;


    }


    function getPostsByCatName($args = false) {
      global $conf;
      if (!isset($args['limit'])) {
        $limit=15;
      }
      else {
        $limit=$args['limit'];
      }
      if (isset($args['page'])) {
        if (intval($args['page'])>1) {
          $page=$args['page']*$limit+1;
        }
        else {
          $page=$args['page'];
        }
      }
      else {
        $page=1;
      }
      $queryArray = $this->db->queryHandler->getQuery("getPostsByCatName");
      //$queryArray = $this->db->queryHandler->getQuery("getLatestPostsView10");


      $dataArray = $this->executeSQL($queryArray,$args['name'],$limit,$page);

      $dataArray = $this->clearHtml($dataArray);

      header('Content-type: application/json',JSON_UNESCAPED_UNICODE);
      echo json_encode($dataArray);
    }

    function getSubCategoryListForParentID($args = false) {
		global $conf;

		$queryArray = $this->db->queryHandler->getQuery("getSubCategoryListForParentID");
		//$queryArray = $this->db->queryHandler->getQuery("getLatestPostsView10");

		$dataArray = $this->executeSQL($queryArray);
		/** header('Content-type: application/json'); */
		echo json_encode($dataArray);
    }


    function getRelatedPosts($args = false) {
      global $conf;

      if (!isset($args['limit'])) {
        $limit=3;
      }
      else {
        $limit=$args['limit'];
      }
      $id=intval($args['id']);
      $page=1;

      $queryArray = $this->db->queryHandler->getQuery("getPostByID");
      //$queryArray = $this->db->queryHandler->getQuery("getLatestPostsView10");

      $dataArray = $this->executeSQL($queryArray,intval($args['id']));
      $id=$dataArray[0]['naslov'];

      $queryArray = $this->db->queryHandler->getQuery("getRelatedPosts");

      $dataArray = $this->executeSQL($queryArray,array($id),$limit,$page);
      $dataArray = $this->clearHtml($dataArray);

      foreach ($dataArray as $k1 => $v1) {
        if (strpos($v1['image'][0],'.jpg')!==false) {
          $img=str_replace('.jpg','-150x150.jpg',$v1['image'][0]);
        }
        elseif(strpos($v1['image'][0],'.png')!==false) {
          $img=str_replace('.png','-150x150.png',$v1['image'][0]);
        }
        elseif(strpos($v1['image'][0],'.jpeg')!==false) {
          $img=str_replace('.jpeg','-150x150.jpeg',$v1['image'][0]);
        }
        elseif(strpos($v1['image'][0],'.gif')!==false) {
          $img=str_replace('.gif','-150x150.gif',$v1['image'][0]);
        }
        else {
          $img=$v1['image'][0];
        }
        $dataArray[$k1]['thumb']=$img;
      }


      /** header('Content-type: application/json'); */
      echo json_encode($dataArray);

    }

    function genericTableManipulation($args = false) {
      global $conf;
      // get the HTTP method, path and body of the request
      $method = $_SERVER['REQUEST_METHOD'];
      $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
      $input = json_decode(file_get_contents('php://input'),true);

      $table=$args['table'];
      if (isset($args['id'])) {
        $key=$args['id'];
      }
      else {
        $key=false;
      }
      
      
      if ($input!=false) {        
        // escape the columns and values from the input object
        $columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
        $values = array_map(function ($value) use ($mysqli) {
          if ($value===null) return null;
          return $value;  
        },array_values($input));

        // build the SET part of the SQL command
        $set = '';
        for ($i=0;$i<count($columns);$i++) {
          $set.=($i>0?',':'').'`'.$columns[$i].'`=';
          $set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
        }
      }
      // create SQL based on HTTP method
      switch ($method) {
        case 'GET':
          if ($key==false) {
            $sqlQuery = "SELECT * FROM `$table`"; break;
          }
          else {
            $sqlQuery = "SELECT * FROM `$table` WHERE id=$key"; break;
          }
          
        case 'PUT':
          $sqlQuery = "UPDATE `$table` SET $set WHERE id=$key"; break;
        case 'POST':
          $sqlQuery = "INSERT INTO `$table` SET $set"; break;
 /*       case 'DELETE':
          $sqlQuery = "DELETE `$table` WHERE id=$key"; break;*/
      }

      // excecute SQL statement
      switch ($method) {
        case 'GET':
          $arrData = $this->db->executeStrSql($sqlQuery);
          echo json_encode($arrData);
          break;
        default:
          $stmt = $this->db->insertUpdateStrSql($sqlQuery);
          echo json_encode($stmt);
          break;
      }
    }

    function getPages($args = false) {
  		global $conf;
      if (!isset($args['limit'])) {
        $limit=15;
      }
      else {
        $limit=$args['limit'];
      }
      if (isset($args['page'])) {
        if (intval($args['page'])>1) {
          $page=$args['page']*$limit+1;
        }
        else {
          $page=$args['page'];
        }
      }
      else {
        $page=1;
      }
  		$queryArray = $this->db->queryHandler->getQuery("getPages");

  		$dataArray = $this->executeSQL($queryArray,false,$limit,$page);
  		$dataArray = $this->clearHtml($dataArray);

  		echo json_encode($dataArray);
    }


    function postmetaTableManipulation($args = false) {
      global $conf;
      // get the HTTP method, path and body of the request
      $method = $_SERVER['REQUEST_METHOD'];
      $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
      $input = json_decode(file_get_contents('php://input'),true);

      $table=$args['table'];
      if (isset($args['post_id'])) {
        $key=$args['post_id'];
      }
      else {
        $key=false;
      }
      
      
      if ($input!=false) {        
        // escape the columns and values from the input object
        $columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
        $values = array_map(function ($value) use ($mysqli) {
          if ($value===null) return null;
          return $value;  
        },array_values($input));

        // build the SET part of the SQL command
        $set = '';
        for ($i=0;$i<count($columns);$i++) {
          $set.=($i>0?',':'').'`'.$columns[$i].'`=';
          $set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
        }
      }
      // create SQL based on HTTP method
      switch ($method) {
        case 'GET':
          if ($key==false) {
            $sqlQuery = "SELECT * FROM wp_postmeta"; break;
          }
          else {
            $sqlQuery = "SELECT * FROM wp_postmeta WHERE post_id=$key"; break;
          }
          
        case 'PUT':
          $sqlQuery = "UPDATE wp_postmeta SET $set WHERE post_id=$key"; break;
        case 'POST':
          $sqlQuery = "INSERT INTO wp_postmeta SET $set"; break;
 /*       case 'DELETE':
          $sqlQuery = "DELETE wp_postmeta WHERE post_id=$key"; break;*/
      }

      // excecute SQL statement
      switch ($method) {
        case 'GET':
          $arrData = $this->db->executeStrSql($sqlQuery);
          echo json_encode($arrData);
          break;
        default:
          $stmt = $this->db->insertUpdateStrSql($sqlQuery);
          echo json_encode($stmt);
          break;
      }
    }

    

  function jsonTopMenu() {
    //$key=md5('top_menu3');
    global $conf;
    $menu=array();

    $arrSQLTopCat = $this->db->queryHandler->getQuery("getTopMenu");
    $arrTopCat = $this->executeSQL($arrSQLTopCat);


    foreach ($arrTopCat as $k1 => $v1) {
      //horizontalni meni
      $menu[$k1]['cat_id']=$v1['term_id'];
    //    $menu[$k1]['url']=$v1['slug'];
      $menu[$k1]['name']=$v1['name'];
      $menu[$k1]['css_class']=$v1['css_class'];;
      $menu[$k1]['slug']=$v1['css_class'];;


      $arrSQLSubCat = $this->db->queryHandler->getQuery("getSubCategoryListForParentID");
      $arrSubCat = $this->executeSQL($arrSQLSubCat,$v1['term_id']);

      $counter=0;
      $menu[$k1]['submenu']=array();
      foreach ($arrSubCat as $k2 => $v2) {
        $menu[$k1]['submenu'][$k2]['cat_id']=$v2['term_id'];
        $menu[$k1]['submenu'][$k2]['url']='category/vesti/'.$v2['slug'];
        $menu[$k1]['submenu'][$k2]['name']=$v2['name'];
        $menu[$k1]['submenu'][$k2]['slug']=$v2['slug'];



        $arrSQLPosts = $this->db->queryHandler->getQuery("getPostsByCatIDwImage");
        $arrPosts = $this->executeSQL($arrSQLPosts,$v2['term_id'],4);

        foreach ($arrPosts as $k3 => $v3) {
          /*
          if (strpos($v3['meta_value'],'.jpg')!==false) {
            $img=str_replace('.jpg','-180x130.jpg',$v3['meta_value']);
          }
          elseif(strpos($v3['meta_value'],'.png')!==false) {
            $img=str_replace('.png','-180x130.png',$v3['meta_value']);
          }
          elseif(strpos($v3['meta_value'],'.jpeg')!==false) {
            $img=str_replace('.jpeg','-180x130.jpeg',$v3['meta_value']);
          }
          elseif(strpos($v3['meta_value'],'.gif')!==false) {
            $img=str_replace('.gif','-180x130.gif',$v3['meta_value']);
          }
          else {
            */
            $img=$v3['meta_value'];
            /*
          }
          */
          $menu[$k1]['submenu'][$k2]['article'][$k3]=array('thumb'=>$img,
          'url'=>$v3['guid'],
          'title'=>$v3['post_title'],
          'post_name'=>$v3['post_name']
          );
        }
      }
    }

  //echo '<pre>';
  //print_r($menu);
  //$this->memcache->set($key, $menu, MEMCACHE_COMPRESSED, 150);
  /** header('Content-type: application/json'); */
  $ret=$this->array2json($menu);
  echo $ret;
}



function jsonFooterMenu() {
  //$key=md5('top_menu3');
  global $conf;
  $menu=array();

  $arrSQLTopCat = $this->db->queryHandler->getQuery("getFooterMenu");
  $arrTopCat = $this->executeSQL($arrSQLTopCat);


  foreach ($arrTopCat as $k1 => $v1) {
    //horizontalni meni
    $menu[$k1]['cat_id']=$v1['term_id'];
    //    $menu[$k1]['url']=$v1['slug'];
    $menu[$k1]['name']=$v1['name'];
    $menu[$k1]['css_class']=$v1['css_class'];;


    $arrSQLSubCat = $this->db->queryHandler->getQuery("getSubCategoryListForParentID");
    $arrSubCat = $this->executeSQL($arrSQLSubCat,$v1['term_id']);

    $counter=0;
    $menu[$k1]['submenu']=array();
    foreach ($arrSubCat as $k2 => $v2) {
      $menu[$k1]['submenu'][$k2]['cat_id']=$v2['term_id'];
      $menu[$k1]['submenu'][$k2]['url']='category/vesti/'.$v2['slug'];
      $menu[$k1]['submenu'][$k2]['name']=$v2['name'];
      $menu[$k1]['submenu'][$k2]['slug']=$v2['slug'];

    }
  }
  /** header('Content-type: application/json'); */
  $ret=$this->array2json($menu);
  echo $ret;
}
/*
**********************************************************************
*******************************  UTILS *******************************
**********************************************************************
*/

function filter($data) {
$data = trim(htmlentities(strip_tags($data)));

if (get_magic_quotes_gpc())
	$data = stripslashes($data);

	//$data = mysql_real_escape_string($data);

return $data;
}

function strip_html_tags( $text )
{
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );
    return strip_tags( $text );
}


function report_error($line,$desc,$data) {
	$errmsg='Detected user error at line '.$line."\r\n";
	$errmsg.='Description: '.$desc."\r\n";
	$errmsg.='Data: '.serialize($data);
	mail('bug@adbuka.com','User error',$errmsg);
}


function extractImage($dataArray) {

  $pattern = '/<img[^>]*src="([^"]*)[^>]*>/i';

  foreach ($dataArray as $k =>$v) {
    preg_match_all($pattern, $dataArray[$k]['vest'], $matches);

    // image src array
    $images = $matches[1];

    foreach ($images as $k2=>$v2){
      $dataArray[$k]['image'][$k2] = $v2;
    }
  }
  return $dataArray;
}

function clearHtml($dataArray) {

  $pattern = '/<img[^>]*src="([^"]*)[^>]*>/i';

  foreach ($dataArray as $k =>$v) {
  	preg_match_all($pattern, $dataArray[$k]['vest'], $matches);

  	// image src array
  	$images = $matches[1];

  	// no images
  	$dataArray[$k]['vest'] = nl2br(strip_tags(preg_replace($pattern, '', str_replace("<br>","\n",$dataArray[$k]['vest'])) ));
    foreach ($images as $k2=>$v2){
      $dataArray[$k]['image'][$k2] = str_replace('http:','',$v2);
    }
  }

  return $dataArray;
}
function clearSerHtml($dataArray) {

  $pattern = '/<img[^>]*src="([^"]*)[^>]*>/i';

  foreach ($dataArray as $k =>$v) {
    preg_match_all($pattern, $dataArray[$k]['vest'], $matches);

    // image src array
    $images = $matches[1];

    // no images
    $dataArray[$k]['vest'] = strip_tags(substr($dataArray[$k]['vest'], 0, 500));
    $lastpos = strrpos(substr($dataArray[$k]['vest'], 0, 150), ' ');
    $dataArray[$k]['vest']  = substr($dataArray[$k]['vest'] , 0, $lastpos) . '...';

    //$dataArray[$k]['vest'] = strip_tags($dataArray[$k]['vest']);
    $dataArray[$k]['image'] = $images;
    $dataArray[$k]['sernum']=$k;
  }
  return $dataArray;
}




function array2json($arr) {
  if(function_exists('json_encode')) return json_encode($arr); //Lastest versions of PHP already has this functionality.
  $parts = array();
  $is_list = false;

  //Find out if the given array is a numerical array
  $keys = array_keys($arr);
  $max_length = count($arr)-1;
  if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1
    $is_list = true;
    for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position
      if($i != $keys[$i]) { //A key fails at position check.
        $is_list = false; //It is an associative array.
        break;
      }
    }
  }

  foreach($arr as $key=>$value) {
    if(is_array($value)) { //Custom handling for arrays
      if($is_list) $parts[] = array2json($value); /* :RECURSION: */
      else $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */
    } else {
      $str = '';
      if(!$is_list) $str = '"' . $key . '":';

      //Custom handling for multiple data types
      if(is_numeric($value)) $str .= $value; //Numbers
      elseif($value === false) $str .= 'false'; //The booleans
      elseif($value === true) $str .= 'true';
      else $str .= '"' . addslashes($value) . '"'; //All other things
      // :TODO: Is there any more datatype we should be in the lookout for? (Object?)

      $parts[] = $str;
      }
    }
    $json = implode(',',$parts);

    if($is_list) return '[' . $json . ']';//Return numerical JSON
    return '{' . $json . '}';//Return associative JSON
  }



/*
**********************************************************************
***************************  SQL UTILS *******************************
**********************************************************************
*/

  function executeSQL ($queryArray,$arrParams=false,$limit=false,$offset=false,$time=90) {

      $stmt=$this->db->prepareSQL($queryArray,$arrParams,$limit,$offset);
      $arrData=$this->db->fetchQueryResultArray($stmt);

/*  
      $key=md5($queryArray['query'].'|'.serialize($arrParams).'|'.$limit.'|'.$offset);
      $get_result = $this->memcached->get($key);
      $get_results=false;
      if ($get_result) {
          $arrData=$get_result;
      }
      else {
      	$stmt=$this->db->prepareSQL($queryArray,$arrParams,$limit,$offset);
      	$arrData=$this->db->fetchQueryResultArray($stmt);
          $this->memcached->set($key, $arrData, $time);
      }
*/  
      return $arrData;
  }

  function executeQuery ($QueryName,$arrParams=false,$limit=false,$offset=false,$time=90) {

      $queryArray=$this->db->queryHandler->getQuery($QueryName);
      $stmt=$this->db->prepareSQL($queryArray,$arrParams,$limit,$offset);
      $arrData=$this->db->fetchQueryResultArray($stmt);
  
  /*
      $key=md5($QueryName.'|'.serialize($arrParams).'|'.$limit.'|'.$offset);
      $get_result = $this->memcached->get($key);
      $get_results=false;
      if ($get_result) {
          $arrData=$get_result;
      }
      else {
          $queryArray=$this->db->queryHandler->getQuery($QueryName);
      	$stmt=$this->db->prepareSQL($queryArray,$arrParams,$limit,$offset);
      	$arrData=$this->db->fetchQueryResultArray($stmt);
          $this->memcached->set($key, $arrData, $time);
      }
  */
      return $arrData;
  }

  function executeQueryReturnID ($QueryName,$arrParams=false,$limit=false,$offset=false) {
  	$queryArray = $this->db->queryHandler->getQuery($QueryName);
  	$stmt = $this->db->prepareSQL($queryArray,$arrParams);
  	$last_id=$stmt->insert_id;
      return $last_id;
  }

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source https://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}


}
?>
