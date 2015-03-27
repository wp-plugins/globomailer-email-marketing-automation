<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>

<script src="<?php echo GMEMA_URL; ?>list/view-list.js"></script>

<?php
$gmema_error_found = FALSE;
$gmema_errors = '';
$gmema_success = '';

$endpoint = new GloboMailerApi_Endpoint_Lists();
if (isset($_POST['frm_gmema_display']) && $_POST['frm_gmema_display'] == 'yes')
{
	$lid = isset($_GET['lid']) ? $_GET['lid'] : '0';
	$action = $endpoint->delete($lid);
	$response_del = $action->body;
	if ($response_del->itemAt('status') == 'success' && !$response_del->itemAt('data')) {
	    $gmema_success = __('List was successfully deleted.', GMEMA_TDOMAIN);
	}else
	{
		$gmema_errors = __('List not deleted please try again.', GMEMA_TDOMAIN);
		$gmema_error_found = TRUE;
	}
}
?>

<div class="wrap">
	<?php
		if ($gmema_error_found == TRUE && $gmema_errors)
		{
			?><div class="error fade"><p><strong><?php echo $gmema_errors; ?></strong></p></div><?php
		}
		if ($gmema_error_found == FALSE && $gmema_success)
		{
			?>
			<div class="updated fade">
				<p>
					<strong><?php echo $gmema_success; ?></strong>
				</p>
			</div>
			<?php
		}
	?>

  <div id="icon-plugins" class="icon32"></div>
  <h2><img src="<?php echo GMEMA_URL ?>img/logo-300x76.png" id="logo-img" width="154px"></h2>
  <div class="tool-box">
  <h3><?php _e('View Lists', GMEMA_TDOMAIN); ?> 
  <a class="add-new-h2" href="<?php echo GMEMA_ADMINURL; ?>?page=gmema-view-list&amp;ac=add"><?php _e('Add New', GMEMA_TDOMAIN); ?></a></h3>
	<?php
	
	$myData = array();
	$response = $endpoint->getLists($pageNumber = 1, $perPage = 100);
	$myData = $response->body->toArray();
	?>
	
    <form name="frm_gmema_display" method="post" onsubmit="return _gmema_bulkaction()">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
            <th scope="col"><?php _e('List UID', GMEMA_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Name', GMEMA_TDOMAIN); ?></th>
			<th scope="col"><?php _e('From Name', GMEMA_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Subject', GMEMA_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Short code', GMEMA_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Action', GMEMA_TDOMAIN); ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th scope="col"><?php _e('List UID', GMEMA_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Name', GMEMA_TDOMAIN); ?></th>
			<th scope="col"><?php _e('From Name', GMEMA_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Subject', GMEMA_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Short code', GMEMA_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Action', GMEMA_TDOMAIN); ?></th>
          </tr>
        </tfoot>
        <tbody>
		<?php 
			if(isset($myData['data']['records']))
			{
				foreach ($myData['data']['records'] as $data)
				{	
					?>
					<tr class="">
					<td><?php echo $data['general']['list_uid']; ?></td>
					<td><?php echo $data['general']['name']; ?></td>
					<td><?php echo $data['defaults']['from_name']; ?></td>     
					<td><?php echo $data['defaults']['subject']; ?></td>
					<td><?php echo '[GloboMailerEMA namefield=YES desc="" list='.$data['general']['list_uid'].']'; ?></td>
					<td>
						<div> 
							<span class="edit">
								<a title="Edit" href="<?php echo GMEMA_ADMINURL; ?>?page=gmema-view-list&amp;ac=edit&amp;lid=<?php echo $data['general']['list_uid']; ?>">
							<?php _e('Edit', GMEMA_TDOMAIN); ?></a> | </span> 
							<span class="trash">
							<a onClick="javascript:_gmema_delete('<?php echo $data['general']['list_uid']; ?>')" href="javascript:void(0);">
							<?php _e('Delete', GMEMA_TDOMAIN); ?></a>
							</span>
						</div>
					</td>
					</tr>
					<?php
				} 
			}
			else
			{
				?>
				<tr>
					<td colspan="7" align="center"><?php _e('No lists available.', GMEMA_TDOMAIN); ?></td>
				</tr>
				<?php 
			}
			?>
        </tbody>
      </table>
      
	<?php wp_nonce_field('gmema_form_show'); ?>
	<input type="hidden" name="frm_gmema_display" value="yes"/> 
	<div style="padding-top:10px;"></div>
    <div class="tablenav">
		<div class="alignright">
			<a class="button add-new-h2" href="<?php echo GMEMA_ADMINURL; ?>?page=gmema-view-list&amp;ac=add"><?php _e('Add New', GMEMA_TDOMAIN);
			 ?></a>
		</div>
    </div>
	</form>
  </div>
</div>