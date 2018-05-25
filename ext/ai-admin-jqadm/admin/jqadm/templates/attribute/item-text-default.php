<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */

$enc = $this->encoder();


?>
<div id="text" class="item-text content-block tab-pane fade" role="tablist" aria-labelledby="text">

	<?php foreach( (array) $this->get( 'textData/langid', [] ) as $idx => $langid ) : ?>

		<div class="group-item card <?= $this->site()->readonly( $this->get( 'textData/siteid/' . $idx ) ); ?>">

			<div id="item-text-group-item-<?= $enc->attr( $idx ); ?>" class="card-header header <?= ( $idx !== 0 ? 'collapsed' : '' ); ?>" role="tab"
				data-toggle="collapse" data-target="#item-text-group-data-<?= $enc->attr( $idx ); ?>"
				aria-expanded="false" aria-controls="item-text-group-data-<?= $enc->attr( $idx ); ?>">
				<div class="card-tools-left">
					<div class="btn btn-card-header act-show fa" tabindex="<?= $this->get( 'tabindex' ); ?>"
						title="<?= $enc->attr( $this->translate( 'admin', 'Show/hide this entry') ); ?>">
					</div>
				</div>
				<span class="item-name-content header-label"><?= $enc->html( $langid . ': ' . $this->get( 'textData/name/content/' . $idx ) ); ?></span>
				&nbsp;
				<div class="card-tools-right">
					<?php if( !$this->site()->readonly( $this->get( 'textData/siteid/' . $idx ) ) ) : ?>
						<div class="btn btn-card-header act-delete fa" tabindex="<?= $this->get( 'tabindex' ); ?>"
							title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry') ); ?>">
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div id="item-text-group-data-<?= $enc->attr( $idx ); ?>" class="card-block collapse row <?= ( $idx === 0 ? 'show' : '' ); ?>"
				role="tabpanel" aria-labelledby="item-text-group-item-<?= $enc->attr( $idx ); ?>">

				<div class="col-xl-6">

					<?php $languages = $this->get( 'pageLangItems', [] ); ?>
					<?php if( count( $languages ) > 1 ) : ?>
						<div class="form-group row mandatory">
							<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Language' ) ); ?></label>
							<div class="col-sm-8">
								<select class="form-control custom-select text-langid" required="required" tabindex="<?= $this->get( 'tabindex' ); ?>"
									name="<?= $enc->attr( $this->formparam( array( 'text', 'langid', '' ) ) ); ?>"
									<?= $this->site()->readonly( $this->get( 'textData/siteid/' . $idx ) ); ?> >
									<option value="">
										<?= $enc->attr( $this->translate( 'admin', 'Please select' ) ); ?>
									</option>

									<?php foreach( $languages as $langItem ) : ?>
										<option value="<?= $enc->attr( $langItem->getCode() ); ?>" <?= ( $langid == $langItem->getCode() ? 'selected="selected"' : '' ) ?> >
											<?= $enc->html( $this->translate( 'client/language', $langItem->getCode() ) ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					<?php else : ?>
						<input class="text-langid" type="hidden"
							name="<?= $enc->attr( $this->formparam( array( 'text', 'langid', '' ) ) ); ?>"
							value="<?= $enc->attr( $langid ); ?>" />
					<?php endif; ?>

					<div class="form-group row optional">
						<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Attribute name' ) ); ?></label>
						<div class="col-sm-8">
							<input class="item-name-listid" type="hidden"
								name="<?= $enc->attr( $this->formparam( array( 'text', 'name', 'listid', '' ) ) ); ?>"
								value="<?= $enc->attr( $this->get( 'textData/name/listid/' . $idx ) ); ?>" />
							<input class="form-control item-name-content" type="text" tabindex="<?= $this->get( 'tabindex' ); ?>"
								name="<?= $enc->attr( $this->formparam( array( 'text', 'name', 'content', '' ) ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'admin', 'Attribute name' ) ); ?>"
								value="<?= $enc->attr( $this->get( 'textData/name/content/' . $idx ) ); ?>"
								<?= $this->site()->readonly( $this->get( 'textData/siteid/' . $idx ) ); ?> />
						</div>
						<div class="col-sm-12 form-text text-muted help-text">
							<?= $enc->html( $this->translate( 'admin', 'Label will be used if empty but then there must be no name for all languages. Otherwise, items will not be shown when sorting by name' ) ); ?>
						</div>
					</div>
					<div class="form-group row optional">
						<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Short description' ) ); ?></label>
						<div class="col-sm-8">
							<input class="item-short-listid" type="hidden"
								name="<?= $enc->attr( $this->formparam( array( 'text', 'short', 'listid', '' ) ) ); ?>"
								value="<?= $enc->attr( $this->get( 'textData/short/listid/' . $idx ) ); ?>" />
							<textarea class="form-control item-short-content" rows="2" tabindex="<?= $this->get( 'tabindex' ); ?>"
								name="<?= $enc->attr( $this->formparam( array( 'text', 'short', 'content', '' ) ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'admin', 'Short description' ) ); ?>"
								 <?= $this->site()->readonly( $this->get( 'textData/siteid/' . $idx ) ); ?>
							 ><?= $enc->attr( $this->get( 'textData/short/content/' . $idx ) ); ?></textarea>
						</div>
					</div>
				</div>

				<div class="col-xl-6">
					<div class="form-group row optional">
						<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Long description' ) ); ?></label>
						<div class="col-sm-8">
							<input class="item-long-listid" type="hidden"
								name="<?= $enc->attr( $this->formparam( array( 'text', 'long', 'listid', '' ) ) ); ?>"
								value="<?= $enc->attr( $this->get( 'textData/long/listid/' . $idx ) ); ?>" />
							<textarea class="form-control htmleditor item-long-content" rows="10" tabindex="<?= $this->get( 'tabindex' ); ?>"
								name="<?= $enc->attr( $this->formparam( array( 'text', 'long', 'content', '' ) ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'admin', 'Long description' ) ); ?>"
								<?= $this->site()->readonly( $this->get( 'textData/siteid/' . $idx ) ); ?>
							><?= $enc->attr( $this->get( 'textData/long/content/' . $idx ) ); ?></textarea>
						</div>
					</div>
				</div>

			</div>
		</div>

	<?php endforeach; ?>

	<div class="group-item card prototype">

		<div id="item-text-group-item-" class="card-header header" role="tab"
			data-toggle="collapse" data-target="#item-text-group-data-"
			aria-expanded="true" aria-controls="item-text-group-data-">
			<div class="card-tools-left">
				<div class="btn btn-card-header act-show fa" tabindex="<?= $this->get( 'tabindex' ); ?>"
					title="<?= $enc->attr( $this->translate( 'admin', 'Show/hide this entry') ); ?>">
				</div>
			</div>
			<span class="item-name-content header-label"></span>
			&nbsp;
			<div class="card-tools-right">
				<div class="btn btn-card-header act-delete fa" tabindex="<?= $this->get( 'tabindex' ); ?>"
					title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry') ); ?>">
				</div>
			</div>
		</div>

		<div id="item-text-group-data-" class="card-block collapse show row" role="tabpanel" aria-labelledby="item-text-group-item-">
			<div class="col-xl-6">

				<?php $languages = $this->get( 'pageLangItems', [] ); ?>
				<?php if( count( $languages ) > 1 ) : ?>
					<div class="form-group row mandatory">
						<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Language' ) ); ?></label>
						<div class="col-sm-8">
							<select class="form-control custom-select text-langid" required="required" tabindex="<?= $this->get( 'tabindex' ); ?>"
								name="<?= $enc->attr( $this->formparam( array( 'text', 'langid', '' ) ) ); ?>" disabled="disabled">
								<option value="">
									<?= $enc->attr( $this->translate( 'admin', 'Please select' ) ); ?>
								</option>

								<?php foreach( $languages as $langItem ) : ?>
									<option value="<?= $enc->attr( $langItem->getCode() ); ?>">
										<?= $enc->html( $this->translate( 'client/language', $langItem->getCode() ) ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php else : $langItem = reset( $languages ); ?>
					<input class="text-langid" type="hidden" disabled="disabled"
						name="<?= $enc->attr( $this->formparam( array( 'text', 'langid', '' ) ) ); ?>"
						value="<?= $enc->attr( $langItem ? $langItem->getId() : '' ); ?>" />
				<?php endif; ?>

				<div class="form-group row optional">
					<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Attribute name' ) ); ?></label>
					<div class="col-sm-8">
						<input class="item-name-listid" type="hidden" disabled="disabled"
							name="<?= $enc->attr( $this->formparam( array( 'text', 'name', 'listid', '' ) ) ); ?>" />
						<input class="form-control item-name-content" type="text" tabindex="<?= $this->get( 'tabindex' ); ?>"
							name="<?= $enc->attr( $this->formparam( array( 'text', 'name', 'content', '' ) ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'admin', 'Attribute name' ) ); ?>" disabled="disabled" />
					</div>
					<div class="col-sm-12 form-text text-muted help-text">
						<?= $enc->html( $this->translate( 'admin', 'Label will be used if empty but then there must be no name for all languages. Otherwise, items will not be shown when sorting by name' ) ); ?>
					</div>
				</div>
				<div class="form-group row optional">
					<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Short description' ) ); ?></label>
					<div class="col-sm-8">
						<input class="item-short-listid" type="hidden" disabled="disabled"
							name="<?= $enc->attr( $this->formparam( array( 'text', 'short', 'listid', '' ) ) ); ?>" />
						<textarea class="form-control item-short-content" rows="2" tabindex="<?= $this->get( 'tabindex' ); ?>"
							name="<?= $enc->attr( $this->formparam( array( 'text', 'short', 'content', '' ) ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'admin', 'Short description' ) ); ?>" disabled="disabled" ></textarea>
					</div>
				</div>
			</div>

			<div class="col-xl-6">
				<div class="form-group row optional">
					<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Long description' ) ); ?></label>
					<div class="col-sm-8">
						<input class="item-long-listid" type="hidden" disabled="disabled"
							name="<?= $enc->attr( $this->formparam( array( 'text', 'long', 'listid', '' ) ) ); ?>" />
						<textarea class="form-control htmleditor-prototype item-long-content" rows="6" tabindex="<?= $this->get( 'tabindex' ); ?>"
							name="<?= $enc->attr( $this->formparam( array( 'text', 'long', 'content', '' ) ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'admin', 'Long description' ) ); ?>" disabled="disabled" ></textarea>
					</div>
				</div>
			</div>

		</div>

	</div>

	<?= $this->get( 'textBody' ); ?>

	<div class="card-tools-more">
		<div class="btn btn-primary btn-card-more act-add fa" tabindex="<?= $this->get( 'tabindex' ); ?>"
			title="<?= $enc->attr( $this->translate( 'admin', 'Insert new entry (Ctrl+I)') ); ?>">
		</div>
	</div>
</div>
