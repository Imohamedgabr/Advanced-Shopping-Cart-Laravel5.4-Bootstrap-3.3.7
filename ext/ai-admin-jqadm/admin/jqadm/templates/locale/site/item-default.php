<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */

$selected = function( $key, $code ) {
	return ( $key == $code ? 'selected="selected"' : '' );
};

$enc = $this->encoder();


$target = $this->config( 'admin/jqadm/url/save/target' );
$cntl = $this->config( 'admin/jqadm/url/save/controller', 'Jqadm' );
$action = $this->config( 'admin/jqadm/url/save/action', 'save' );
$config = $this->config( 'admin/jqadm/url/save/config', [] );

$params = $this->get( 'pageParams', [] );


?>
<?php $this->block()->start( 'jqadm_content' ); ?>

<form class="item item-locale-site form-horizontal" method="POST" enctype="multipart/form-data" action="<?= $enc->attr( $this->url( $target, $cntl, $action, $params, [], $config ) ); ?>">
	<input id="item-id" type="hidden" name="<?= $enc->attr( $this->formparam( array( 'item', 'locale.site.id' ) ) ); ?>" value="<?= $enc->attr( $this->get( 'itemData/locale.site.id' ) ); ?>" />
	<input id="item-next" type="hidden" name="<?= $enc->attr( $this->formparam( array( 'next' ) ) ); ?>" value="get" />
	<?= $this->csrf()->formfield(); ?>

	<nav class="main-navbar">
		<span class="navbar-brand">
			<?= $enc->html( $this->translate( 'admin', 'Site' ) ); ?>:
			<?= $enc->html( $this->get( 'itemData/locale.site.id' ) ); ?> -
			<?= $enc->html( $this->get( 'itemData/locale.site.label', $this->translate( 'admin', 'New' ) ) ); ?>
		</span>
		<div class="item-actions">
			<?= $this->partial( $this->config( 'admin/jqadm/partial/itemactions', 'common/partials/itemactions-default.php' ), ['params' => $params] ); ?>
		</div>
	</nav>

	<div class="row item-container">

		<div class="col-md-3 item-navbar">
			<ul class="nav nav-tabs flex-md-column flex-wrap d-flex justify-content-between" role="tablist">

				<li class="nav-item basic">
					<a class="nav-link active" href="#basic" data-toggle="tab" role="tab" aria-expanded="true" aria-controls="basic">
						<?= $enc->html( $this->translate( 'admin', 'Basic' ) ); ?>
					</a>
				</li>

				<?php foreach( array_values( $this->get( 'itemSubparts', [] ) ) as $idx => $subpart ) : ?>
					<li class="nav-item <?= $enc->attr( $subpart ); ?>">
						<a class="nav-link" href="#<?= $enc->attr( $subpart ); ?>" data-toggle="tab" role="tab" tabindex="<?= ++$idx+1; ?>">
							<?= $enc->html( $this->translate( 'admin', $subpart ) ); ?>
						</a>
					</li>
				<?php endforeach; ?>

			</ul>

			<div class="item-meta text-muted">
				<small>
					<?= $enc->html( $this->translate( 'admin', 'Modified' ) ); ?>:
					<span class="meta-value"><?= $enc->html( $this->get( 'itemData/locale.site.mtime' ) ); ?></span>
				</small>
				<small>
					<?= $enc->html( $this->translate( 'admin', 'Created' ) ); ?>:
					<span class="meta-value"><?= $enc->html( $this->get( 'itemData/locale.site.ctime' ) ); ?></span>
				</small>
				<small>
					<?= $enc->html( $this->translate( 'admin', 'Editor' ) ); ?>:
					<span class="meta-value"><?= $enc->html( $this->get( 'itemData/locale.site.editor' ) ); ?></span>
				</small>
			</div>
		</div>

		<div class="col-md-9 item-content tab-content">
			<div id="basic" class="row item-basic tab-pane fade show active" role="tabpanel" aria-labelledby="basic">

				<div class="col-xl-6 content-block">
					<div class="form-group row mandatory">
						<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Status' ) ); ?></label>
						<div class="col-sm-8">
							<select class="form-control custom-select item-status" required="required" tabindex="1"
								name="<?= $enc->attr( $this->formparam( array( 'item', 'locale.site.status' ) ) ); ?>" >
								<option value="">
									<?= $enc->attr( $this->translate( 'admin', 'Please select' ) ); ?>
								</option>
								<option value="1" <?= $selected( $this->get( 'itemData/locale.site.status', 1 ), 1 ); ?> >
									<?= $enc->html( $this->translate( 'admin', 'status:enabled' ) ); ?>
								</option>
								<option value="0" <?= $selected( $this->get( 'itemData/locale.site.status', 1 ), 0 ); ?> >
									<?= $enc->html( $this->translate( 'admin', 'status:disabled' ) ); ?>
								</option>
								<option value="-1" <?= $selected( $this->get( 'itemData/locale.site.status', 1 ), -1 ); ?> >
									<?= $enc->html( $this->translate( 'admin', 'status:review' ) ); ?>
								</option>
								<option value="-2" <?= $selected( $this->get( 'itemData/locale.site.status', 1 ), -2 ); ?> >
									<?= $enc->html( $this->translate( 'admin', 'status:archive' ) ); ?>
								</option>
							</select>
						</div>
					</div>
					<div class="form-group row mandatory">
						<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Code' ) ); ?></label>
						<div class="col-sm-8">
							<input class="form-control item-code" required="required" tabindex="1" autocomplete="off"
								name="<?= $enc->attr( $this->formparam( array( 'item', 'locale.site.code' ) ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'admin', 'Unique site code (required)' ) ); ?>"
								value="<?= $enc->attr( $this->get( 'itemData/locale.site.code' ) ); ?>" />
						</div>
						<div class="col-sm-12 form-text text-muted help-text">
							<?= $enc->html( $this->translate( 'admin', 'Code to uniquely identify the site' ) ); ?>
						</div>
					</div>
					<div class="form-group row mandatory">
						<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Label' ) ); ?></label>
						<div class="col-sm-8">
							<input class="form-control item-label" required="required" tabindex="1" autocomplete="off"
								name="<?= $enc->attr( $this->formparam( array( 'item', 'locale.site.label' ) ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'admin', 'Label (required)' ) ); ?>"
								value="<?= $enc->attr( $this->get( 'itemData/locale.site.label' ) ); ?>" />
						</div>
						<div class="col-sm-12 form-text text-muted help-text">
							<?= $enc->html( $this->translate( 'admin', 'Descritive name of the site' ) ); ?>
						</div>
					</div>
				</div><!--

				--><div class="col-xl-6 content-block">
					<table class="item-config table table-striped">
						<thead>
							<tr>
								<th>
									<span class="help"><?= $enc->html( $this->translate( 'admin', 'Option' ) ); ?></span>
									<div class="form-text text-muted help-text">
										<?= $enc->html( $this->translate( 'admin', 'Site specific configuration options, will be available as key/value pairs' ) ); ?>
									</div>
								</th>
								<th>
									<?= $enc->html( $this->translate( 'admin', 'Value' ) ); ?>
								</th>
								<th class="actions">
									<div class="btn act-add fa" tabindex="1"
										title="<?= $enc->attr( $this->translate( 'admin', 'Insert new entry (Ctrl+I)') ); ?>">
									</div>
								</th>
							</tr>
						</thead>
						<tbody>

							<?php foreach( (array) $this->get( 'itemData/config/key', [] ) as $idx => $key ) : ?>
								<tr class="config-item">
									<td>
										<input type="text" class="config-key form-control" tabindex="1"
											name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'key', '' ) ) ); ?>"
											value="<?= $enc->attr( $this->get( 'itemData/config/key/' . $idx, $key ) ); ?>" />
									</td>
									<td>
										<input type="text" class="config-value form-control" tabindex="1"
											name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'val', '' ) ) ); ?>"
											value="<?= $enc->attr( $this->get( 'itemData/config/val/' . $idx ) ); ?>" />
									</td>
									<td class="actions">
										<div class="btn act-delete fa" tabindex="1"
											title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry') ); ?>">
										</div>
									</td>
								</tr>
							<?php endforeach; ?>

							<tr class="prototype">
								<td>
									<input type="text" class="config-key form-control" tabindex="1" disabled="disabled"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'key', '' ) ) ); ?>" />
								</td>
								<td>
									<input type="text" class="config-value form-control" tabindex="1" disabled="disabled"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'val', '' ) ) ); ?>" />
								</td>
								<td class="actions">
									<div class="btn act-delete fa" tabindex="1"
										title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry') ); ?>">
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

			</div>

			<?= $this->get( 'itemBody' ); ?>

		</div>

		<div class="item-actions">
			<?= $this->partial( $this->config( 'admin/jqadm/partial/itemactions', 'common/partials/itemactions-default.php' ), ['params' => $params] ); ?>
		</div>
	</div>
</form>

<?php $this->block()->stop(); ?>


<?= $this->render( $this->config( 'admin/jqadm/template/page', 'common/page-default.php' ) ); ?>
