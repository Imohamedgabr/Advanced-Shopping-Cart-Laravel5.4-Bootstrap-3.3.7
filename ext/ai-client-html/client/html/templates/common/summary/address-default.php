<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2017
 */

/* Available data:
 * - address : Order address item
 * - type : Type of the address item, i.e. "delivery" or "payment"
 */


$enc = $this->encoder();
$addr = $this->address;

$salutations = array(
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR,
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MRS,
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MISS,
);

echo preg_replace( "/\n+/m", "<br/>", trim( $enc->html( sprintf(
	/// Address format with company (%1$s), salutation (%2$s), title (%3$s), first name (%4$s), last name (%5$s),
	/// address part one (%6$s, e.g street), address part two (%7$s, e.g house number), address part three (%8$s, e.g additional information),
	/// postal/zip code (%9$s), city (%10$s), state (%11$s), country (%12$s), language (%13$s),
	/// e-mail (%14$s), phone (%15$s), facsimile/telefax (%16$s), web site (%17$s), vatid (%18$s)
	$this->translate( 'client', '%1$s
%2$s %3$s %4$s %5$s
%6$s %7$s
%8$s
%9$s %10$s
%11$s
%12$s
%13$s
%14$s
%15$s
%16$s
%17$s
%18$s
'
	),
	$addr->getCompany(),
	( in_array( $addr->getSalutation(), $salutations ) ? $this->translate( 'client/code', $addr->getSalutation() ) : '' ),
	$addr->getTitle(),
	$addr->getFirstName(),
	$addr->getLastName(),
	$addr->getAddress1(),
	$addr->getAddress2(),
	$addr->getAddress3(),
	$addr->getPostal(),
	$addr->getCity(),
	$addr->getState(),
	$this->translate( 'client/country', $addr->getCountryId() ),
	$this->translate( 'client/language', $addr->getLanguageId() ),
	$addr->getEmail(),
	$addr->getTelephone(),
	$addr->getTelefax(),
	$addr->getWebsite(),
	$addr->getVatID()
) ) ) );
