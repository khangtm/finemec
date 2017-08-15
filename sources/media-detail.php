<?php  

if(!defined('_source')) die("Error");

	@$id_danhmuc =  trim(strip_tags(addslashes($_GET['id_danhmuc'])));
	@$id_list =   trim(strip_tags(addslashes($_GET['id_list'])));
	@$id_cat =   trim(strip_tags(addslashes($_GET['id_cat'])));
	@$id_item =   trim(strip_tags(addslashes($_GET['id_item'])));
	@$id =   trim(strip_tags(addslashes($_GET['id'])));
	$type_link = '';
	$tbl = 'news_danhmuc';
	if($type=='dichvu'){
		$type_link = 'dm1';
	}else{
		$type_link = 'bv';
	}

    if($id!='')
	{
        
                //Cập nhật lượt xem
		$d->reset();
		$sql_lanxem = "UPDATE #_news SET luotxem=luotxem+1 WHERE id ='$id'";
		$d->query($sql_lanxem);
		
		//Chi tiết loai tin tức
		$d->reset();
		$sql_detail = "select id,ten$lang as ten, tenkhongdau, id_danhmuc, ngaytao, mail, tel, title, luotxem,noidung$lang as noidung FROM #_news where hienthi=1 and id='$id' limit 0,1";
		$d->query($sql_detail);
		$row_detail = $d->fetch_array();
		if(empty($row_detail)){redirect("http://".$config_url.'/404.php');}	
		$media = $row_detail;
                
                // loai tin tuc
                $d->reset();
		$sql_list = "select id,ten$lang as ten, tenkhongdau FROM #_news_danhmuc where hienthi=1 and type='tintuc' and id= ".$media['id_danhmuc']." order by stt asc limit 1";
		$d->query($sql_list);
		$row_list = $d->fetch_array();
		$loaiTinTuc = $row_list;
                
                // list loai tin tuc
                $d->reset();
		$sql_list = "select id,ten$lang as ten, tenkhongdau FROM #_news_danhmuc where hienthi=1 and type='tintuc'  order by stt asc ";
		$d->query($sql_list);
		$row_list_loai = $d->result_array();
		$listLoaiTinTuc = $row_list_loai;
		
                
                // list all tin tuc
                $d->reset();
		$sql_list_tintuc = "select id,ten$lang as ten, tenkhongdau, mota$lang as mota,noidung$lang as noidung, noibat, stt, title, ngaytao,luotxem FROM #_news where type='tintuc' and hienthi=1 and id_danhmuc = $id order by stt,id desc";
		$d->query($sql_list_tintuc);
		$listTinTuc = $d->result_array();
                
                
		
		#Thông tin share facebook
		$images_facebook = 'http://'.$config_url.'/'._upload_tintuc_l.$row_detail['photo'];
		$title_facebook = $row_detail['ten'];
		$description_facebook = trim(strip_tags($row_detail['mota']));
		$url_facebook = getCurrentPageURL();
		
		//Hình ảnh khác của tin tức
		$d->reset();
		$sql_hinhthem = "select id,ten$lang as ten,thumb,photo FROM #_hinhanh where id_hinhanh='".$row_detail['id']."' and type='".$type."' and hienthi=1 order by stt,id desc";
		$d->query($sql_hinhthem);
		$hinhthem = $d->result_array();
		
		//Đánh giá sao
		$d->reset();
		$sql = "select ROUND(AVG(giatri)) as giatri FROM #_danhgiasao where link='".getCurrentPageURL()."' order by time desc";
		$d->query($sql);
		$danhgiasao = $d->fetch_array();
		
		if($danhgiasao['giatri']<6){$num_danhgiasao=6;}else{$num_danhgiasao=$danhgiasao['giatri'];};
		
		//tin tức cùng loại
		$tbl = 'news';
		$where = " type='".$type."'";	
		if(!empty($row_detail['id_danhmuc'])){
			$where .= " and id_danhmuc='".$row_detail['id_danhmuc']."'";
		}
		if(!empty($row_detail['id_list'])){
			$where .= " and id_list='".$row_detail['id_list']."'";
		}
		$where .= " and id<>'$id' and hienthi=1 order by stt,id desc";
	}
	//Danh mục tin tức cấp 4
	elseif($id_item!='')
	{
		$d->reset();
		$sql = "select id,ten$lang as ten,title,keywords,description FROM #_news_item where id='$id_item' limit 0,1";
		$d->query($sql);
		$title_bar = $d->fetch_array();
		if(empty($title_bar)){redirect("http://".$config_url.'/404.php');}
		
		$title_cat = $title_bar['ten'];
		$title = $title_bar['title'];
		$keywords = $title_bar['keywords'];
		$description = $title_bar['description'];
	
		$where = " type='".$type."' and id_item='$id_item' and hienthi=1 order by stt,id desc";
	}
	//Danh mục tin tức cấp 3
	elseif($id_cat!='')
	{
		$d->reset();
		$sql = "select id,ten$lang as ten,title,keywords,description FROM #_news_cat where id='$id_cat' limit 0,1";
		$d->query($sql);
		$title_bar = $d->fetch_array();
		if(empty($title_bar)){redirect("http://".$config_url.'/404.php');}
		
		$title_cat = $title_bar['ten'];
		$title = $title_bar['title'];
		$keywords = $title_bar['keywords'];
		$description = $title_bar['description'];
	
		$where = " type='".$type."' and id_cat='$id_cat' and hienthi=1 order by stt,id desc";
	}
	//Danh mục tin tức cấp 2
	elseif($id_list!='')
	{
		$d->reset();
		$sql = "select id,ten$lang as ten,title,keywords,description FROM #_news_list where id='$id_list' limit 0,1";
		$d->query($sql);
		$title_bar = $d->fetch_array();
		if(empty($title_bar)){redirect("http://".$config_url.'/404.php');}
		
		$title_cat = $title_bar['ten'];
		$title = $title_bar['title'];
		$keywords = $title_bar['keywords'];
		$description = $title_bar['description'];

		$tbl = 'news';
		$type_link = 'bv';
		$where = " type='".$type."' and id_list='$id_list' and hienthi=1 order by stt,id desc";
	}
	
	//Danh mục tin tức cấp 1
	else if($id_danhmuc!='')
	{		
		$d->reset();
		$sql = "select id,ten$lang as ten,title,keywords,description FROM #_news_danhmuc where id='$id_danhmuc' limit 0,1";
		$d->query($sql);
		$title_bar = $d->fetch_array();
		if(empty($title_bar)){redirect("http://".$config_url.'/404.php');}
					
		$title_cat = $title_bar['ten'];
		$title = $title_bar['title'];
		$keywords = $title_bar['keywords'];
		$description = $title_bar['description'];
		
		if($type=='dichvu'){
			$tbl = 'news_list';
			$type_link = 'dm2';
		}else{
			$tbl = 'news';
		}
		
		$where = " type='".$type."' and id_danhmuc='$id_danhmuc' and hienthi=1 order by stt,id desc";
	}
	//Tất cả tin tức
	else
	{
		$where = " type='".$type."' and hienthi=1 order by stt,id desc";
	}

		if($type=='dichvu'){
			#Lấy tin tức và phân trang
			$d->reset();
			$sql = "SELECT count(id) AS numrows FROM #_$tbl where $where";
			$d->query($sql);	
			$dem = $d->fetch_array();
		}else{
			#Lấy tin tức và phân trang
			$d->reset();
			$sql = "SELECT count(id) AS numrows FROM #_news where $where";
			$d->query($sql);	
			$dem = $d->fetch_array();
		}
	
		
		
		$totalRows = $dem['numrows'];
		$page = $_GET['p'];
		if($id > 0)
		{
			$pageSize = $company['soluong_tink'];//Số item cho 1 trang
		}
		else
		{
			$pageSize = $company['soluong_tin'];//Số item cho 1 trang
		}
		$offset = 5;//Số trang hiển thị				
		if ($page == "")$page = 1;
		else $page = $_GET['p'];
		$page--;
		$bg = $pageSize*$page;		
		
		if($type=='dichvu'){
			$d->reset();
			$sql = "select id,ten$lang as ten,tenkhongdau,mota$lang as mota,photo,thumb FROM #_$tbl where $where limit $bg,$pageSize";		
			$d->query($sql);
			$tintuc = $d->result_array();

			if(!empty($id_danhmuc) && empty($tintuc)){

				$tbl = 'news';
				$type_link = 'bv';
				$where = " type='".$type."' and id_danhmuc='$id_danhmuc' and hienthi=1 order by stt,id desc";

				$d->reset();
				$sql = "select id,ten$lang as ten,tenkhongdau,mota$lang as mota,photo,thumb FROM #_$tbl where $where limit $bg,$pageSize";	

				$d->query($sql);
				$tintuc = $d->result_array();
			}
			$url_link = getCurrentPageURL();
			
		}else{
			$d->reset();
			$sql = "select id,ten$lang as ten,tenkhongdau,thumb,photo,mota$lang as mota,ngaytao FROM #_news where $where limit $bg,$pageSize";		
			$d->query($sql);
			$tintuc = $d->result_array();	
			$url_link = getCurrentPageURL();

		}

	
?>