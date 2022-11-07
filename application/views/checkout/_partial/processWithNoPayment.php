<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="-padding-20 -align-center">
	<h3><?php echo Label::getLabel('LBL_Payment'); ?></h3>
</div>

<div class="-padding-20 -align-center -no-padding-top">
	<div class="-border -padding-20">
			<div class="boxwhite">
				<p><?php echo Label::getLabel('LBL_Payment_to_be_made'); ?></p>
				<h6><?php echo CommonHelper::displayMoneyFormat($cartData['orderNetAmount']); ?></h6>
			</div>
			<div class="boxwhite">
				<?php 
				$confirmForm->setFormTagAttribute( 'class', 'form' );
				$btnSubmitFld = $confirmForm->getField('btn_submit');
				$btnSubmitFld->addFieldTagAttribute('class', 'btn btn--secondary btn--large');
				
				$confirmForm->developerTags['colClassPrefix'] = 'col-md-';
				$confirmForm->developerTags['fld_default_col'] = 12;
				
				echo $confirmForm->getFormHtml(); ?>
			</div>
			

		
	</div>
</div>