<?php
class TransactionAction extends Action {
	public function index(){
        $Data = M('transaction'); // 实例化Data数据模型
		$Data2 = M('product');
		$Data3 = M('transaction_order');
		$Data4 = M('user');
		if( $_POST )
		{
			$checkselect = $_POST['checktype'];
			$starttime = $_POST['starttime'];
			$endtime = $_POST['endtime'];
			$statusnum=count($checkselect);
			$map='STATUS = 0';
			for ($i=0; $i<$statusnum; $i++)
			{
				$map=$map.' OR ';
				$map=$map.'STATUS = '.$checkselect[$i];
			}
			if($starttime&&$endtime)
				$map2 = "TIMESTAMP BETWEEN ".strtotime($starttime)." AND ".strtotime($endtime);
			elseif($starttime)
				$map2 = "TIMESTAMP >= ".strtotime($starttime);
			elseif($endtime)
				$map2 = "TIMESTAMP <= ".strtotime($endtime);
			else
				$map2=array();
			$Transaction = $Data->where($map2)->having($map)->select();
		}
		else
		{
			$Transaction = $Data->select();
		}
		$transactionsum=count($Transaction);
		for($i=0; $i<$transactionsum; $i++)
		{
			$data[$i]['TIMESTAMP']=date('Y-m-d',$Transaction[$i]['TIMESTAMP']);
			$Order=$Data3->where('TID='.$Transaction[$i]['TID'])->select();
			$Ordersum=count($Order);
			$data[$i]['PRICE']=0;
			$Product=array();
			for($j=0; $j<$Ordersum; $j++)
			{
				$Product=$Data2->where('PID = '.$Order[$j]['PID'])->find();
				$data[$i]['PRICE'] += $Product['PRICE']*$Order[$j]['NUM'];
				$data[$i]['PRODUCT']=$data[$i]['PRODUCT'].$Product['NAME'].'X'.$Order[$j]['NUM'].'<br>';
			}
			$data[$i]['NO']=$Transaction[$i]['TID'];
			$user=$Data4->where('UID = '.$Transaction[$i]['SUID'])->find();
			$data[$i]['SELLER']=$user['USERNAME'];
			$user=$Data4->where('UID = '.$Transaction[$i]['BUID'])->find();
			$data[$i]['BUYER']=$user['USERNAME'];
			switch ($Transaction[$i]['STATUS'])
			{
			case 1:
				$data[$i]['STATUS']='预订';
				break;
			case 2:
				$data[$i]['STATUS']='已付款';
				break;
			case 3:
				$data[$i]['STATUS']='已发货';
				break;
			}
		}
		$this->transactions = $data;
        $this->display();
    }
	
	public function complaint_result($TID=0){
		$Data_t = M('transaction'); // 实例化Data数据模型
		$Data_u = M('user');
		$Data_c = M('complaint');
		$data=$Data_t->where('TID = '.$TID)->find();
		$buyer=$Data_u->where('UID = '.$data[BUID])->find();
		$seller=$Data_u->where('UID = '.$data[SUID])->find();
		$complaint=$Data_c->where('TID = '.$TID)->find();
		$user=$Data_u->where('UID = '.$complaint[CID])->find();
		$this->complaint_TID=$TID;
		$this->complaint_BUYER=$buyer['USERNAME'];
		$this->complaint_SELLER=$seller['USERNAME'];
		$this->complaint_TIME=date('Y-m-d',$complaint['TIMESTAMP']);
		$this->complaint_USER=$user['USERNAME'];
		$this->complaint_REASON=$complaint['REASON'];
		trace($user);
		switch ($complaint['STATUS'])
		{
		case 1:
			$this->complaint_STATUS='正在处理';
			break;
		case 2:
			$this->complaint_STATUS='已处理';
			break;
		}
		$this->display();
	}
	
	public function order($TID=0){
		$Data_t = M('transaction'); // 实例化Data数据模型
		$Data_p = M('product');
		$Data_o = M('transaction_order');
		$Data_u = M('user');
		$Transaction=$Data_t->where('TID = '.$TID)->find();
		$user=$Data_u->where('UID = '.$Transaction['SUID'])->find();
		$Seller=$user['USERNAME'];
		$user=$Data_u->where('UID = '.$Transaction['BUID'])->find();
		$Buyer=$user['USERNAME'];
		$Order=$Data_o->where('TID='.$Transaction['TID'])->select();
		$Ordersum=count($Order);
		for($i=0;$i<$Ordersum;$i++)
		{
			$Product=$Data_p->where('PID = '.$Order[$i]['PID'])->find();
			$ToltalPrice += $Product['PRICE']*$Order[$i]['NUM'];
			$Products[$i]=$Product['NAME'].'X'.$Order[$i]['NUM'];
		}
		$time=date('Y-m-d',$Transaction['TIMESTAMP']);
		$State=$Transaction['STATUS'];
		switch ($State)
		{
		case 1:
			$this->Order_STATUS='预订';
			break;
		case 2:
			$this->Order_STATUS='已付款';
			break;
		case 3:
			$this->Order_STATUS='已发货';
			break;
		}
		$this->Order_TID = $TID;
		$this->Order_SELLER = $Seller;
		$this->Order_BUYER = $Buyer;
		$this->Order_PRODUCTS = $Products;
		$this->Order_PRICE = $ToltalPrice;
		trace($Products);
		$this->display();
	}
}
