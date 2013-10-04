<!-- generate form -->
<?php if(isset($model) && !empty($model) && isset($result)) :?>

<?php
// Generate a layout
// $num_columns must be valid, i.e. (1|2|3|4|6|12)
/*
$myKeys = $editKeys;

$default_num_columns = 2;
$num_columns = isset($num_columns) ? $num_columns : $default_num_columns;
$num_columns = $num_columns > 12 ? 12 : $num_columns;
$num_columns = $num_columns <  1 ?  1 : $num_columns;
$num_columns = !in_array($num_columns, array(1, 2, 3, 4, 6, 12)) ? $default_num_columns : $num_columns;
$num_fields  = count( $myKeys );
$span_size   = 12 / $num_columns;
if($num_fields > 0) :
	if($num_fields >= 8)
		$myKeys = array_chunk($myKeys, ceil($num_fields / $num_columns));
	else // if too few keys, multiple columns are hideous
		$myKeys = array( $myKeys, array() ) ;
else :
	$this->load->view('includes/message', array('message' => array(
		'type' => 'error',
		'title' => 'Error!',
		'text' => 'An error has occurred generating this form.'
	)));
endif;
*/
$basicKeys = array(
	'type',
	'enabled',
	'name',
	'url'
);

$settingsKeys = array(
	'player_minoto_id',
	'theme',
	'logo_url',
	'color_1',
	'color_2',
	'publish_url_1',
	'publish_url_2'
);

$thisUrl = $directoryName.'/'.$controllerName.'/edit/'.$result['minoto_id'];

?>

<div class="container">
	<h3>Edit <?php echo $modelName ?></h3>
	<div class="row">
		<div class="span12">
			<div class="tabbable tabs-left">
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-basic" data-toggle="tab">Basic</a></li>
	<li><a href="#tab-settings" data-toggle="tab">Settings</a></li>
    <!--<li><a href="#tab-metadata" data-toggle="tab">Metadata</a></li>-->
	<li><a href="#tab-whitelist" data-toggle="tab">IP Whitelist</a></li>
	<li><a href="#tab-whitelist-domains" data-toggle="tab">Domains Whitelist</a></li>
</ul>
<div class="tab-content">
	<!-- basic -->
	<div class="tab-pane fade in active" id="tab-basic">
		<form class="form-horizontal ajax-form-simple" action="<?php echo $thisUrl; ?>" method="post" accept-charset="utf-8">
			<div class="my-message"></div>
			<div class="row">
				<div class="span6">
					<input type="hidden" name="request" value="basic">
					<input type="hidden" name="minoto_id" value="<?php echo $result['minoto_id'] ?>">
					<?php
					foreach($basicKeys as $key):
						$field = $fields[$key];
						$field['name']  = $key;
						$field['value'] = $field['value'] = isset($result) && isset($result[$key]) ? $result[$key] : (isset($field['value']) ? $field['value'] : '');
						$field['value'] = html_escape($field['value']);
						echo input( $field );
					endforeach;
					?>
				</div>
			</div>

			<div class="form-actions">
				<div class="pull-right">
					<!--<a class="btn" href="<?php echo $directoryName?>/<?php echo $controllerName ?>"><i class="icon-remove"></i> Cancel</a>-->
					<button type="submit" class="btn btn-primary" data-loading-text="Saving ..."><i class="icon-ok icon-white"></i> Save</button>
				</div>
			</div>
		</form>
	</div>
	<!-- /basic -->
	<!-- settings -->
	<div class="tab-pane fade" id="tab-settings">

		<form class="form-horizontal ajax-form-simple" action="<?php echo $thisUrl; ?>" method="post" accept-charset="utf-8">
			<div class="my-message"></div>
			<div class="row">
				<div class="span6">
					<input type="hidden" name="request" value="settings">
					<input type="hidden" name="minoto_id" value="<?php echo $result['minoto_id'] ?>">
					<?php
					foreach($settingsKeys as $key):
						$field = $fields[$key];
						$field['name']  = $key;
						$field['value'] = $field['value'] = isset($result) && isset($result[$key]) ? $result[$key] : (isset($field['value']) ? $field['value'] : '');
						$field['value'] = html_escape($field['value']);
						echo input( $field );
					endforeach;
					?>
				</div>
			</div>

			<div class="form-actions">
				<div class="pull-right">
					<!--<a class="btn" href="<?php echo $directoryName?>/<?php echo $controllerName ?>"><i class="icon-remove"></i> Cancel</a>-->
					<button type="submit" class="btn btn-primary" data-loading-text="Saving ..."><i class="icon-ok icon-white"></i> Save</button>
				</div>
			</div>
		</form>

	</div>
	<!-- /settings -->
	<!-- metadata -->
	<div class="tab-pane fade" id="tab-metadata">

<div class="alert alert-info">
<button type="button" class="close" data-dismiss="alert">&times;</button>
Custom metadata. This is a work in progress...
</div>

	<p>
		<a class="btn btn-primary metadata-modal-create"><i class="icon-white icon-plus"></i>  New Metadata</a>
	</p>

<?php 
$all_metadatas  = $this->db->order_by('sort_order', 'asc')->where('organisation_id',$result['id'])->get('video_metadata')->result();
$metadataFields = array(
	'sort_order' => array(
		'label' => 'Order',
		'type' => 'text'
	),
	'name' => array(
		'label' => 'Name',
		'type' => 'text'
	),
	'label' => array(
		'label' => 'Label',
		'type' => 'text'
	),
	'type' => array(
		'label' => 'Type',
		'type' => 'select',
		'values' => array(
			'text' => 'Text',
			'select' => 'Select',
			'datetime' => 'Date + Time',
			'tags' => 'Tags'
		),
		'value' => 'text'
	),
	'values' => array(
		'label' => 'Values',
		'type' => 'text'
	),
	'value' => array(
		'label' => 'Default Value',
		'type' => 'text'
	)
);
$metadataTableKeys = array('sort_order','name','label','type','value');
$metadataKeys = array('sort_order','name','label','type','values','value');

?>
<?php if($all_metadatas) : ?>
<table class="table table-hover">
	<thead>
		<tr>
			<?php foreach($metadataTableKeys as $key) : ?>
			<th><?php echo $metadataFields[$key]['label']; ?></th>
			<?php endforeach; ?>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($all_metadatas as $item) : ?>
		<tr class="">
			<?php foreach($metadataTableKeys as $key) : ?>
			<td><?php echo html_escape($item->$key); ?></td>
			<?php endforeach; ?>
			<td class="row-actions text-right">
				<form class="form-inline" style="margin:0;padding:0;" action="<?php echo $thisUrl ?>" method="post">
					<input type="hidden" name="request" value="metadata">
					<input type="hidden" name="action" value="remove" />
					<input type="hidden" name="id" value="<?php echo $item->id; ?>" />
					<a class="btn btn-small metadata-modal-edit"
						data-metadata-id="<?php echo $item->id; ?>"
						data-sort_order="<?php echo $item->sort_order; ?>"
						data-name="<?php echo html_escape($item->name); ?>"
						data-label="<?php echo html_escape($item->label); ?>"
						data-type="<?php echo html_escape($item->type); ?>"
						data-values="<?php echo html_escape($item->values); ?>"
						data-value="<?php echo html_escape($item->value); ?>"
						data-toggle="tooltip" title="Edit"><i class="icon-pencil"></i></a>
					<button type="submit" class="btn btn-small confirmation-modal" data-toggle="tooltip" title="Remove"><i class="icon-trash"></i></button>
				</form>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else : ?>
<div class="well">
<p class="text-center">No metadata found.</p>
</div>
<?php endif;?>

<!-- Metadata Modal -->
<div id="metadata-modal" class="modal hide fade" tabindex="-1">
	<form class="form-horizontal <!--ajax-form-simple-->" style="margin:0;padding:0;" action="<?php echo $thisUrl; ?>" method="post">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Metadata</h3>
	</div>
	<div class="modal-body">
		<div class="my-message"></div>
		<input type="hidden" name="request" value="metadata">
		<input type="hidden" name="action" value="create">
		<input type="hidden" name="id" value="">

		<?php
			foreach($metadataKeys as $key){
					$field = $metadataFields[$key];
					$field['name']  = $key;
					echo input($field);
			}
		?>
	</div>
	<div class="modal-footer">
		<a class="btn" data-dismiss="modal">Cancel</a>
		<button type="submit" class="btn btn-primary">Update</button>
	</div>
	</form>
</div>
<script>
	$('#metadata-modal').modal({
		keyboard : true,
		show: false,
		backdrop: true // 'static' iff true modal
	});
	
	$('a.metadata-modal-create').click(function(e){
		e.preventDefault();
		$('#metadata-modal').find('input[name="action"]').val('create');
		$('#metadata-modal').find('input[name="id"]').val('');
		$('#metadata-modal').find('input[name="sort_order"]').val('');
		$('#metadata-modal').find('input[name="name"]').val('');
		$('#metadata-modal').find('input[name="label"]').val('');
		$('#metadata-modal').find('input[name="type"]').val('');
		$('#metadata-modal').find('input[name="values"]').val('');
		$('#metadata-modal').find('input[name="value"]').val('');
		$('#metadata-modal').find('button[type="submit"]').html('Create');
		$('#metadata-modal').modal('show');
	});

	$('a.metadata-modal-edit').click(function(e){
		e.preventDefault();
		$('#metadata-modal').find('input[name="action"]').val('edit');
		$('#metadata-modal').find('input[name="id"]').val($(this).data('metadata-id'));
		$('#metadata-modal').find('input[name="sort_order"]').val($(this).data('sort_order'));
		$('#metadata-modal').find('input[name="name"]').val($(this).data('name'));
		$('#metadata-modal').find('input[name="label"]').val($(this).data('label'));
		$('#metadata-modal').find('input[name="type"]').val($(this).data('type'));
		$('#metadata-modal').find('input[name="values"]').val($(this).data('values'));
		$('#metadata-modal').find('input[name="value"]').val($(this).data('value'));
		$('#metadata-modal').find('button[type="submit"]').html('Update');
		$('#metadata-modal').modal('show');
	});


</script>
<!-- /Metadata Modal -->
	</div>
	<!-- /metadata -->
	<!-- whitelist -->
	<div class="tab-pane fade" id="tab-whitelist">
	<p>
		<a class="btn btn-primary whitelist-modal-create"><i class="icon-white icon-plus"></i>  Add IP</a>
	</p>

<?php 
$all_whitelists = $this->db->get_where('whitelist', array('organisation_id' => $result['id']))->result();
//print_r($all_whitelists);

if($all_whitelists) {
	// print all whitelist items.
}
else {
	// none found.
}
$whitelistFields = $this->whitelist_model->fields;
$whitelistTableKeys = array('ip', 'description');
?>
<?php if($all_whitelists) : ?>
<table class="table table-hover">
	<thead>
		<tr>
			<?php foreach($whitelistTableKeys as $key) : ?>
			<th><?php echo $whitelistFields[$key]['label']; ?></th>
			<?php endforeach; ?>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($all_whitelists as $whitelist_item) : ?>
		<tr class="">
			<?php foreach($whitelistTableKeys as $key) : ?>
			<td><?php echo html_escape($whitelist_item->$key); ?></td>
			<?php endforeach; ?>
			<td class="row-actions text-right">
				<form class="form-inline" style="margin:0;padding:0;" action="<?php echo $thisUrl ?>" method="post">
					<input type="hidden" name="request" value="whitelist">
					<input type="hidden" name="action" value="remove" />
					<input type="hidden" name="id" value="<?php echo $whitelist_item->id; ?>" />
					<a class="btn btn-small whitelist-modal-edit" data-whitelist-id="<?php echo $whitelist_item->id; ?>" data-description="<?php echo $whitelist_item->description; ?>" data-ip="<?php echo $whitelist_item->ip; ?>" data-toggle="tooltip" title="Edit"><i class="icon-pencil"></i></a>
					<button type="submit" class="btn btn-small confirmation-modal" data-toggle="tooltip" title="Remove"><i class="icon-trash"></i></button>
				</form>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else : ?>
<div class="well">
<p class="text-center">No whitelist items found.</p>
</div>
<?php endif;?>

<!-- Whitelist Modal -->
<div id="whitelist-modal" class="modal hide fade" tabindex="-1">
	<form class="form-horizontal <!--ajax-form-simple-->" style="margin:0;padding:0;" action="<?php echo $thisUrl; ?>" method="post">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Whitelist IP</h3>
	</div>
	<div class="modal-body">
		<div class="my-message"></div>
		<input type="hidden" name="request" value="whitelist">
		<input type="hidden" name="action" value="create">
		<input type="hidden" name="id" value="">

		<?php
			$whitelistFields = $this->whitelist_model->fields;
			$whitelistKeys = array('ip', 'description');
			foreach($whitelistKeys as $key){
					$field = $whitelistFields[$key];
					$field['name']  = $key;
					echo input($field);
			}
		?>
	</div>
	<div class="modal-footer">
		<a class="btn" data-dismiss="modal">Cancel</a>
		<button type="submit" class="btn btn-primary">Update</button>
	</div>
	</form>
</div>
<script>
	$('#whitelist-modal').modal({
		keyboard : true,
		show: false,
		backdrop: true // 'static' iff true modal
	});
	
	$('a.whitelist-modal-create').click(function(e){
		e.preventDefault();
		$('#whitelist-modal').find('input[name="action"]').val('create');
		$('#whitelist-modal').find('input[name="id"]').val('');
		$('#whitelist-modal').find('input[name="ip"]').val('');
		$('#whitelist-modal').find('input[name="description"]').val('');
		$('#whitelist-modal').find('button[type="submit"]').html('Create');
		$('#whitelist-modal').modal('show');
	});

	$('a.whitelist-modal-edit').click(function(e){
		e.preventDefault();
		$('#whitelist-modal').find('input[name="action"]').val('edit');
		$('#whitelist-modal').find('input[name="id"]').val($(this).data('whitelist-id'));
		$('#whitelist-modal').find('input[name="ip"]').val($(this).data('ip'));
		$('#whitelist-modal').find('input[name="description"]').val($(this).data('description'));
		$('#whitelist-modal').find('button[type="submit"]').html('Update');
		$('#whitelist-modal').modal('show');
	}); // need to refresh page ?


</script>
<!-- /Whitelist Modal -->
	</div>
	<!-- /whitelist -->
	
	
	
	<!-- whitelist-domains -->
	<div class="tab-pane fade" id="tab-whitelist-domains">
	<p>
		<a class="btn btn-primary whitelist-domain-modal-create"><i class="icon-white icon-plus"></i>  Add Domain</a>
	</p>

<?php 
$all_whitelists = $this->db->get_where('whitelist_domain', array('organisation_id' => $result['id']))->result();
//print_r($all_whitelists);

$whitelistFields = $this->whitelistdomain_model->fields;
$whitelistTableKeys = array('domain', 'description');
?>
<?php if($all_whitelists) : ?>
<table class="table table-hover">
	<thead>
		<tr>
			<?php foreach($whitelistTableKeys as $key) : ?>
			<th><?php echo $whitelistFields[$key]['label']; ?></th>
			<?php endforeach; ?>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($all_whitelists as $whitelist_item) : ?>
		<tr class="">
			<?php foreach($whitelistTableKeys as $key) : ?>
			<td><?php echo html_escape($whitelist_item->$key); ?></td>
			<?php endforeach; ?>
			<td class="row-actions text-right">
				<form class="form-inline" style="margin:0;padding:0;" action="<?php echo $thisUrl ?>" method="post">
					<input type="hidden" name="request" value="whitelistdomain">
					<input type="hidden" name="action" value="remove" />
					<input type="hidden" name="id" value="<?php echo $whitelist_item->id; ?>" />
					<a class="btn btn-small whitelist-domain-modal-edit" data-whitelist-id="<?php echo $whitelist_item->id; ?>" data-description="<?php echo $whitelist_item->description; ?>" data-domain="<?php echo $whitelist_item->domain; ?>" data-toggle="tooltip" title="Edit"><i class="icon-pencil"></i></a>
					<button type="submit" class="btn btn-small confirmation-modal" data-toggle="tooltip" title="Remove"><i class="icon-trash"></i></button>
				</form>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else : ?>
<div class="well">
<p class="text-center">No whitelist items found.</p>
</div>
<?php endif;?>

<!-- Whitelist Modal -->
<div id="whitelist-modal-domain" class="modal hide fade" tabindex="-1">
	<form class="form-horizontal <!--ajax-form-simple-->" style="margin:0;padding:0;" action="<?php echo $thisUrl; ?>" method="post">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Whitelist Domain</h3>
	</div>
	<div class="modal-body">
		<div class="my-message"></div>
		<input type="hidden" name="request" value="whitelistdomain">
		<input type="hidden" name="action" value="create">
		<input type="hidden" name="id" value="">

		<?php
			$whitelistFields = $this->whitelistdomain_model->fields;
			$whitelistKeys = array('domain', 'description');
			foreach($whitelistKeys as $key){
					$field = $whitelistFields[$key];
					$field['name']  = $key;
					echo input($field);
			}
		?>
	</div>
	<div class="modal-footer">
		<a class="btn" data-dismiss="modal">Cancel</a>
		<button type="submit" class="btn btn-primary">Update</button>
	</div>
	</form>
</div>
<script>
	var whitelistdomainmodal = '#whitelist-modal-domain';
	$(whitelistdomainmodal).modal({
		keyboard : true,
		show: false,
		backdrop: true // 'static' iff true modal
	});
	
	$('a.whitelist-domain-modal-create').click(function(e){
		e.preventDefault();
		$(whitelistdomainmodal).find('input[name="action"]').val('create');
		$(whitelistdomainmodal).find('input[name="id"]').val('');
		$(whitelistdomainmodal).find('input[name="domain"]').val('');
		$(whitelistdomainmodal).find('input[name="description"]').val('');
		$(whitelistdomainmodal).find('button[type="submit"]').html('Create');
		$(whitelistdomainmodal).modal('show');
	});

	$('a.whitelist-domain-modal-edit').click(function(e){
		e.preventDefault();
		$(whitelistdomainmodal).find('input[name="action"]').val('edit');
		$(whitelistdomainmodal).find('input[name="id"]').val($(this).data('whitelist-id'));
		$(whitelistdomainmodal).find('input[name="domain"]').val($(this).data('domain'));
		$(whitelistdomainmodal).find('input[name="description"]').val($(this).data('description'));
		$(whitelistdomainmodal).find('button[type="submit"]').html('Update');
		$(whitelistdomainmodal).modal('show');
	}); // need to refresh page ?


</script>
<!-- /Whitelist Modal -->
	</div>
	<!-- /whitelist -->
	
	
	
</div>
			</div><!-- /.tabbable -->
		</div><!-- /.span12 -->
	</div><!-- /.row -->
</div><!-- /.container -->
<!--
	<pre>
	<?php print_r($result); ?>
	</pre>
-->
<?php endif; ?>