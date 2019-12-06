<?php
class S_View_Helper_ShowWidgets extends Zend_View_Helper_Abstract
{
	public function ShowWidgets($mname, $cname, $aname)
	{
		$action = new Zend_View_Helper_Action();
		echo '<script type="text/javascript">
			$(function(){
				$("a#widgetChooser").fancybox({
					type: \'iframe\',
					onClosed: function(){
						window.location.reload();
					}
				});
			});
			</script>
			
			<a style="margin-left:10px; margin-top:20px;" href="/reports/report/widget/mname/'.$mname.'/cname/'.$cname.'/aname/'.$aname.'" class="widgetChooser" id="widgetChooser"></a>
		';
		
		echo $action->action('cwidget', 'report','reports',array('mname'=>$mname,'cname'=>$cname,'aname'=>$aname));
	}
}