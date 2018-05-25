<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */

$attr = function( $list, $key, $code ) {
	$map = ( isset( $list[$key] ) ? $list[$key]->toArray() : [] );
	return ( isset( $map[$code] ) ? $map[$code] : '' );
};

$selected = function( $key, $code ) {
	return ( $key == $code ? 'selected="selected"' : '' );
};

$enc = $this->encoder();


$target = $this->config( 'admin/jqadm/url/save/target' );
$cntl = $this->config( 'admin/jqadm/url/save/controller', 'Jqadm' );
$action = $this->config( 'admin/jqadm/url/save/action', 'save' );
$config = $this->config( 'admin/jqadm/url/save/config', [] );

$attributes = $this->get( 'itemAttributes', [] );
$params = $this->get( 'pageParams', [] );


?>
<?php $this->block()->start( 'jqadm_content' ); ?>

<form class="item item-coupon form-horizontal" method="POST" enctype="multipart/form-data" action="<?= $enc->attr( $this->url( $target, $cntl, $action, $params, [], $config ) ); ?>">
	<input id="item-id" type="hidden" name="<?= $enc->attr( $this->formparam( array( 'item', 'coupon.id' ) ) ); ?>" value="<?= $enc->attr( $this->get( 'itemData/coupon.id' ) ); ?>" />
	<input id="item-next" type="hidden" name="<?= $enc->attr( $this->formparam( array( 'next' ) ) ); ?>" value="get" />
	<?= $this->csrf()->formfield(); ?>

	<nav class="main-navbar">
		<span class="navbar-brand">
			<?= $enc->html( $this->translate( 'admin', 'Voucher' ) ); ?>:
			<?= $enc->html( $this->get( 'itemData/coupon.id' ) ); ?> -
			<?= $enc->html( $this->get( 'itemData/coupon.label', $this->translate( 'admin', 'New' ) ) ); ?>
			<span class="navbar-secondary">(<?= $enc->html( $this->site()->match( $this->get( 'itemData/coupon.siteid' ) ) ); ?>)</span>
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
					<span class="meta-value"><?= $enc->html( $this->get( 'itemData/coupon.mtime' ) ); ?></span>
				</small>
				<small>
					<?= $enc->html( $this->translate( 'admin', 'Created' ) ); ?>:
					<span class="meta-value"><?= $enc->html( $this->get( 'itemData/coupon.ctime' ) ); ?></span>
				</small>
				<small>
					<?= $enc->html( $this->translate( 'admin', 'Editor' ) ); ?>:
					<span class="meta-value"><?= $enc->html( $this->get( 'itemData/coupon.editor' ) ); ?></span>
				</small>
			</div>
		</div>

		<div class="col-md-9 item-content tab-content">

			<div id="basic" class="row item-basic tab-pane fade show active" role="tabpanel" aria-labelledby="basic">

				<div class="col-xl-6 content-block <?= $this->site()->readonly( $this->get( 'itemData/coupon.siteid' ) ); ?>">
					<div class="form-group row mandatory">
						<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Status' ) ); ?></label>
						<div class="col-sm-8">
							<select class="form-control custom-select item-status" required="required" tabindex="1"
								name="<?= $enc->attr( $this->formparam( array( 'item', 'coupon.status' ) ) ); ?>"
								<?= $this->site()->readonly( $this->get( 'itemData/coupon.siteid' ) ); ?> >
								<option value="">
									<?= $enc->html( $this->translate( 'admin', 'Please select' ) ); ?>
								</option>
								<option value="1" <?= $selected( $this->get( 'itemData/coupon.status', 1 ), 1 ); ?> >
									<?= $enc->html( $this->translate( 'admin', 'status:enabled' ) ); ?>
								</option>
								<option value="0" <?= $selected( $this->get( 'itemData/coupon.status', 1 ), 0 ); ?> >
									<?= $enc->html( $this->translate( 'admin', 'status:disabled' ) ); ?>
								</option>
								<option value="-1" <?= $selected( $this->get( 'itemData/coupon.status', 1 ), -1 ); ?> >
									<?= $enc->html( $this->translate( 'admin', 'status:review' ) ); ?>
								</option>
								<option value="-2" <?= $selected( $this->get( 'itemData/coupon.status', 1 ), -2 ); ?> >
									<?= $enc->html( $this->translate( 'admin', 'status:archive' ) ); ?>
								</option>
							</select>
						</div>
					</div>
					<div class="form-group row mandatory">
						<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Label' ) ); ?></label>
						<div class="col-sm-8">
							<input class="form-control item-label" type="text" required="required" tabindex="1"
								name="<?= $this->formparam( array( 'item', 'coupon.label' ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'admin', 'Internal name (required)' ) ); ?>"
								value="<?= $enc->attr( $this->get( 'itemData/coupon.label' ) ); ?>"
								<?= $this->site()->readonly( $this->get( 'itemData/coupon.siteid' ) ); ?> />
						</div>
						<div class="col-sm-12 form-text text-muted help-text">
							<?= $enc->html( $this->translate( 'admin', 'Internal article name, will be used on the web site if no product name for the language is available' ) ); ?>
						</div>
					</div>
					<div class="form-group row mandatory">
						<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Provider' ) ); ?></label>
						<div class="col-sm-8 input-group">
							<input class="form-control item-provider combobox" type="text" required="required" tabindex="1"
								name="<?= $enc->attr( $this->formparam( array( 'item', 'coupon.provider' ) ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'admin', 'Provider/decorator class names (required)' ) ); ?>"
								value="<?= $enc->attr( $this->get( 'itemData/coupon.provider' ) ); ?>"
								data-names="<?= implode( ',', $this->get( 'itemProviders', [] ) ); ?>"
								<?= $this->site()->readonly( $this->get( 'itemData/coupon.siteid' ) ); ?> />
							<div class="dropdown input-group-addon">
								<a class="dropdown-add" id="decoratorButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
								<div class="dropdown-menu dropdown-menu-right" aria-labelledby="decoratorButton">
									<?php foreach( $this->get( 'itemDecorators', [] ) as $name ) : ?>
										<a class="dropdown-item decorator-name" href="#" data-name="<?= $enc->attr( $name ); ?>"><?= $enc->html( $name ); ?></a>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
						<div class="col-sm-12 form-text text-muted help-text">
							<?= $enc->html( $this->translate( 'admin', 'One provider and zero or more decorator class names separated by commas' ) ); ?>
						</div>
					</div>
					<div class="form-group row optional">
						<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Start date' ) ); ?></label>
						<div class="col-sm-8">
							<input class="form-control item-datestart" type="datetime-local" tabindex="1"
								name="<?= $enc->attr( $this->formparam( array( 'item', 'coupon.datestart' ) ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'admin', 'YYYY-MM-DD hh:mm:ss (optional)' ) ); ?>"
								value="<?= $enc->attr( str_replace( ' ', 'T', $this->get( 'itemData/coupon.datestart' ) ) ); ?>"
								<?= $this->site()->readonly( $this->get( 'itemData/coupon.siteid' ) ); ?> />
						</div>
						<div class="col-sm-12 form-text text-muted help-text">
							<?= $enc->html( $this->translate( 'admin', 'The article is only shown on the web site after that date and time, useful or seasonal articles' ) ); ?>
						</div>
					</div>
					<div class="form-group row optional">
						<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'End date' ) ); ?></label>
						<div class="col-sm-8">
							<input class="form-control item-dateend" type="datetime-local" tabindex="1"
								name="<?= $enc->attr( $this->formparam( array( 'item', 'coupon.dateend' ) ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'admin', 'YYYY-MM-DD hh:mm:ss (optional)' ) ); ?>"
								value="<?= $enc->attr( str_replace( ' ', 'T', $this->get( 'itemData/coupon.dateend' ) ) ); ?>"
								<?= $this->site()->readonly( $this->get( 'itemData/coupon.siteid' ) ); ?> />
						</div>
						<div class="col-sm-12 form-text text-muted help-text">
							<?= $enc->html( $this->translate( 'admin', 'The article is only shown on the web site until that date and time, useful or seasonal articles' ) ); ?>
						</div>
					</div>
				</div><!--

				--><div class="col-xl-6 content-block <?= $this->site()->readonly( $this->get( 'itemData/coupon.siteid' ) ); ?>">
					<table class="item-config table table-striped">
						<thead>
							<tr>
								<th class="config-row-key">
									<span class="help"><?= $enc->html( $this->translate( 'admin', 'Option' ) ); ?></span>
									<div class="form-text text-muted help-text">
										<?= $enc->html( $this->translate( 'admin', 'Coupon provider or coupon decorator configuration name' ) ); ?>
									</div>
								</th>
								<th class="config-row-value">
									<?= $enc->html( $this->translate( 'admin', 'Value' ) ); ?>
								</th>
								<th class="actions">
									<?php if( !$this->site()->readonly( $this->get( 'itemData/coupon.siteid' ) ) ) : ?>
										<div class="btn act-add fa" tabindex="1"
											title="<?= $enc->attr( $this->translate( 'admin', 'Insert new entry (Ctrl+I)') ); ?>">
										</div>
									<?php endif; ?>
								</th>
							</tr>
						</thead>
						<tbody>

							<?php foreach( (array) $this->get( 'itemData/config/key', [] ) as $idx => $key ) : ?>
								<tr class="config-item">
									<td class="config-row-key">
										<input type="text" class="config-key form-control" tabindex="1"
											name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'key', '' ) ) ); ?>"
											value="<?= $enc->attr( $this->get( 'itemData/config/key/' . $idx, $key ) ); ?>"
											<?= $this->site()->readonly( $this->get( 'itemData/coupon.siteid' ) ); ?> />
										<div class="form-text text-muted help-text"></div>
									</td>
									<td class="config-row-value">
										<?php $cfgval = $this->get( 'itemData/config/val/' . $idx ); ?>
										<input type="text" class="config-value form-control config-type" tabindex="1"
											name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'val', '' ) ) ); ?>"
											value="<?= $enc->attr( !is_scalar( $cfgval ) ? json_encode( $cfgval ) : $cfgval ); ?>"
											<?= $this->site()->readonly( $this->get( 'itemData/coupon.siteid' ) ); ?> />
									</td>
									<td class="actions">
										<?php if( !$this->site()->readonly( $this->get( 'itemData/coupon.siteid' ) ) ) : ?>
											<div class="btn act-delete fa" tabindex="1"
												title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry') ); ?>">
											</div>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>

							<tr class="config-item prototype">
								<td class="config-row-key">
									<input type="text" class="config-key form-control" tabindex="1" disabled="disabled"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'key', '' ) ) ); ?>" />
									<div class="form-text text-muted help-text"></div>
								</td>
								<td class="config-row-value">

									<div class="config-type config-type-map">
										<input type="text" class="config-value form-control" tabindex="1" disabled="disabled"
											name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'val', '' ) ) ); ?>" />

										<table class="table table-striped config-map-table">
											<tr class="config-map-row prototype-map">
												<td class="config-map-actions">
													<div class="btn act-delete fa" tabindex="1"
														title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry') ); ?>">
													</div>
												</td>
												<td class="config-map-row-key">
													<input type="text" class="config-map-key form-control" tabindex="1" disabled="disabled" name="" />
												</td>
												<td class="config-map-row-value">
													<input type="text" class="config-map-value form-control" tabindex="1" disabled="disabled" name="" />
												</td>
											</tr>
											<tr class="config-map-actions">
												<td class="config-map-action-add">
													<div class="btn act-add fa" tabindex="1"
														title="<?= $enc->attr( $this->translate( 'admin', 'Insert new entry') ); ?>">
													</div>
												</td>
												<td class="config-map-action-update" colspan="2">
													<div class="btn btn-primary act-update" tabindex="1">
														<?= $enc->attr( $this->translate( 'admin', 'OK') ); ?>
													</div>
												</td>
											</tr>
										</table>
									</div>

									<select class="config-value form-control config-type config-type-select" tabindex="1" disabled="disabled"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'val', '' ) ) ); ?>" >
									</select>

									<select class="config-value form-control config-type config-type-boolean" tabindex="1" disabled="disabled"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'val', '' ) ) ); ?>" >
										<option value=""></option>
										<option value="0"><?= $enc->html( $this->translate( 'client', 'no' ) ); ?></option>
										<option value="1"><?= $enc->html( $this->translate( 'client', 'yes' ) ); ?></option>
									</select>

									<input type="text" class="config-value form-control config-type config-type-string" tabindex="1" disabled="disabled"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'val', '' ) ) ); ?>" />

									<input type="number" class="config-value form-control config-type config-type-number" tabindex="1" disabled="disabled"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'val', '' ) ) ); ?>" step="0.01" />

									<input type="number" class="config-value form-control config-type config-type-integer" tabindex="1" disabled="disabled"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'val', '' ) ) ); ?>" />

									<input type="date" class="config-value form-control config-type config-type-date" tabindex="1" disabled="disabled"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'val', '' ) ) ); ?>" />

									<input type="datetime-local" class="config-value form-control config-type config-type-datetime" tabindex="1" disabled="disabled"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'config', 'val', '' ) ) ); ?>" />

									<input type="time" class="config-value form-control config-type config-type-time" tabindex="1" disabled="disabled"
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
