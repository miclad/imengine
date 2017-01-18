<style type="text/css">
.mailing_content{padding:20px; font-family:tahoma; font-size:12px; color:#191919;}
.mailing_content table{font-family:tahoma; font-size:12px; color:#191919; border-collapse:collapse;}
.mailing_content td{padding:0;}
.mailing_content a:link{color:#4F7809; text-decoration:underline;}
.mailing_content a:visited{color:#4F7809; text-decoration:underline;}
.mailing_content a:hover{color:#4F7809; text-decoration:none;}
.mailing_content hr{line-height:1px; height:1px; border:none; background:#737373; color:#737373; margin:18px 0;}
.mailing_content h1{color:#000; font-size:18px; margin:0; padding:0; font-weight:normal; font-family:tahoma; margin-bottom:18px; font-family:arial;}
.mailing_content h2{color:#000; font-size:20px; margin:0; padding:0; font-weight:normal; font-family:tahoma; margin-bottom:7px; font-family:arial;}
.mailing_content h2 a{font-size:20px;}
.mailing_content p{margin-top:0px; padding-top:0px; margin-bottom:20px;}

.mailing_content .mailing_header{margin-bottom:40px;}
.mailing_content .mailing_header td{font-size:10px; color:#8D8D8D; line-height:1.4em;}
.mailing_content .mailing_header td.mailing_logo{width:232px;}
.mailing_content .mailing_header td a{font-size:10px;}
.mailing_content .mailing_topic .ph{width:285px;}
.mailing_content .mailing_topic div{padding-right:20px;}
.mailing_content .mailing_footer{font-size:10px;}
.mailing_content .mailing_footer a{font-size:10px; line-height:1.6em;}

.mailing_content .news_table td{padding-right:30px; vertical-align:top;}
.mailing_content .news_table span{font-size:11px; color:#8D8D8D;}
.mailing_content .news_table div{padding-bottom:10px;}
.mailing_content .news_table b{display:block; padding-bottom:4px; line-height:1.4em;}

.mailing_content .to{font-size:14px; padding-bottom:3px;}

.product_card{width:154px; float:left; height:auto !important; height:320px; min-height:320px; margin:0 20px 15px 0px; display:inline;}
.mailing_content .product_card{height:auto !important; height:320px; min-height:320px;}
.mailing_content .product_card img{border:none;}
.product_card .product_name{padding-top:12px;}
.product_card .product_name a{font-size:14px; font-weight:bold;}
.product_card .short_desc{padding:3px 0 0 0; font-size:11px; color:#737373;}
.price{color:#000; font-size:14px; font-weight:bold; padding-top:3px;}

<?/*
span.rub{font-weight:normal; text-transform:uppercase; font-family:'tahoma' !important; background:url('_img/dot1.gif') left 70% repeat-x;}
span.hyphen:after{content:'\2013';}
span.hyphen{font-weight:normal; position:absolute; margin:.3ex -1px 0; behavior:expression(this.innerHTML = '&ndash;');}
*/?>

img{border:none;}
</style>

<div class="mailing_content">

	<?if(true || $this->is_inner){?>
		<table class="mailing_header">
			<tr>
				<td class="mailing_logo">
					<a href="http://<?=$_SERVER['SERVER_NAME']?>/"><img src="http://<?=$_SERVER['SERVER_NAME']?>/img/dastore.gif"/></a>
				</td>
			</tr>
		</table>
	<?}?>
	
	<div class="to">Здравствуйте<?=$data['name'] ? ', '.$data['name'] : ''?>!</div>
	<h1>Отписка от рыссылки daStore.ru</h1>
	
	<hr/>
	
	<div style="line-height:1.5em;">
		<?$lnk = 'http://'.$_SERVER['SERVER_NAME'].'/subscribe/?a=unsubscribe&code='.$code.'&id='.$data['id']?>
		Для подтверждения отказа от рассылки перейдите по ссылке<br>
		<a href="<?=$lnk?>"><?=$lnk?></a>
	</div>
	
	<hr/>
	
	
	
	<div class="mailing_footer">
		Магазин для профессионалов кино, телевидения и видеографов. Камкордеры, объективы, аккумуляторы, студийный свет...
	</div>

</div>