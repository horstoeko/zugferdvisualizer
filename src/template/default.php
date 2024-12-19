<?php
//@phan-var $document \horstoeko\zugferd\ZugferdDocumentReader
//@phan-var $transformer \horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerCodelistTransform
?>
<html>
	<head>
		<style>
			@page {
				size: 21cm 29cm;
				margin-left: 2.5cm;
			}
			body {
				font-size: 9pt;
			}
			body h1 {
				font-size: 19pt;
				margin: 0;
				padding: 0;
				margin-top: 50px
			}
			body h2{
				font-size: 11pt;
				font-weight: bold;
			}
			table {
				margin: 0;
				padding: 0;
				table-layout: fixed;
			}
			tr {
				margin: 0;
				padding: 0;
			}
			th, td {
				vertical-align: top;
			}
			th {
				margin-left: 0;
				margin-right: 0;
				padding-left: 0;
				padding-right: 0;
				font-size: 8pt;
			}
			td {
				font-size: 8pt;
			}
			table.space tbody tr:first-child td{
				padding-top: 10px;
			}
			table.postable {
				width: 100%;
				min-width: 100%;
				max-width: 100%;
				margin-top: 5px;
			}
			table.postable th {
				padding-bottom: 10px;
				text-align:left;
			}
			table.postable td.posno,
			table.postable th.posno {
				width: 10%;
				min-width: 10%;
				max-width: 10%;
				text-align: left;
			}
			table.postable td.posdesc,
			table.postable th.posdesc {
				width: 25%;
				min-width: 25%;
				max-width: 25%;
				text-align: left;
			}
			table.postable td.posqty,
			table.postable th.posqty ,
			table.postable td.posunitprice,
			table.postable th.posunitprice,
			table.postable td.poslineamount,
			table.postable th.poslineamount {
				width: 20%;
				min-width: 20%;
				max-width: 20%;
				text-align: right;
			}
			table.postable td.poslinevat,
			table.postable th.poslinevat {
				width: 5%;
				min-width: 5%;
				max-width: 5%;
				text-align: right;
			}
			table.postable th.posno,
			table.postable th.posdesc,
			table.postable th.posqty,
			table.postable th.posunitprice,
			table.postable th.poslineamount,
			table.postable th.poslinevat {
				border-bottom: 1px solid #dcdcdc;
			}
			table.postable td.totalname {
				width: 20%;
				min-width: 20%;
				max-width: 20%;
				text-align: left;
				border-bottom: 1px solid #dcdcdc;
			}
			table td.totalvalue {
				text-align: right;
			}
			table.postable td.totalvalue {
				width: 20%;
				min-width: 20%;
				max-width: 20%;
			}
			div.parallel{
				display: flex;
				justify-content: space-between;
				align-items: flex-start;
			}
			div.prodDesc{
				font-style: italic;
				font-size: 80%;
			}
			.bold {
				font-weight: bold;
			}
			.italic {
				font-style: italic;
			}
			.red {
				color: #ff0000;
			}
			.green {
				color: #00fff0
			}
		</style>
	</head>
	<body>
		<?php
		$document->getDocumentInformation($documentno, $documenttypecode, $documentdate, $invoiceCurrency, $taxCurrency, $documentname, $documentlanguage, $effectiveSpecifiedPeriod);
		$document->getDocumentBuyer($buyername, $buyerids, $buyerdescription);
		$document->getDocumentBuyerAddress($buyeraddressline1, $buyeraddressline2, $buyeraddressline3, $buyerpostcode, $buyercity, $buyercounty, $buyersubdivision);
		$document->getDocumentBuyerOrderReferencedDocument($bt13, $btX147);
		//leitwegeID bt10
		$document->getDocumentBuyerReference($buyerRef);
		$document->getDocumentSupplyChainEvent($bt72);
		$document->getDocumentSeller($sellerName, $sellerId, $sellerDescription);
		$document->getDocumentSellerAddress($sellerLineOne, $sellerLineTwo, $sellerLineThree, $sellerPostCode, $sellerCity, $sellerCountry, $sellerSubDivision);
		?>
		<div class="parallel">
			<p>
				<?=
				implode('<br/>', array_filter([
					$buyername,
					$buyeraddressline1,
					$buyeraddressline2,
					$buyeraddressline3,
					$buyercounty . ' ' . $buyerpostcode . ' ' . $buyercity
				]));
				?>
			</p>
			<p>
				<?=
				implode('<br/>', array_filter([
					$sellerName,
					$sellerSubDivision,
					$sellerLineOne,
					$sellerLineTwo,
					$sellerLineThree,
					$sellerCountry . ' ' . $sellerPostCode . ' ' . $sellerCity,
				]));
				?>
			</p>
		</div>
		<h1>
<?= $transformer->transformDocTypeCode($documenttypecode); ?>
		</h1>
		<p>
		<table class="postable">
			<thead>
				<tr>
					<th><?= $transformer->getString('Invoice date'); ?></th>
					<th><?= $transformer->getString('Delivery date'); ?></th>
					<th><?= $transformer->getString('Invoice no'); ?></th>
					<th><?= $transformer->getString('Customer no'); ?></th>
					<th><?= $transformer->getString('Reference'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?= $transformer->formatDate($documentdate); ?></td>
					<td><?= $transformer->formatDate($bt72) ?: $transformer->formatDate($documentdate); /* optional, filled by invoice date */ ?></td>
					<td><?= $documentno; ?></td>
					<td><?= implode(', ', $buyerids); ?></td>
					<td><?= $buyerRef . $bt13; ?></td>
				</tr>
			</tbody>
		</table>
	</p>

	<table class="postable space">
		<thead>
			<tr>
				<th class="posno"><?= $transformer->getString('Pos.'); ?></th>
				<th class="posdesc"><?= $transformer->getString('Description'); ?></th>
				<th class="posqty"><?= $transformer->getString('Qty'); ?></th>
				<th class="posunitprice"><?= $transformer->getString('Price'); ?></th>
				<th class="poslinevat"><?= $transformer->getString('VAT'); ?>&nbsp;%</th>
				<th class="poslineamount"><?= $transformer->getString('Amount'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($document->firstDocumentPosition()){
				do{
					$document->getDocumentPositionGenerals($lineid, $linestatuscode, $linestatusreasoncode);
					$document->getDocumentPositionProductDetails($prodname, $proddesc, $prodsellerid, $prodbuyerid, $prodglobalidtype, $prodglobalid);
					$document->getDocumentPositionGrossPrice($grosspriceamount, $grosspricebasisquantity, $grosspricebasisquantityunitcode);
					$document->getDocumentPositionNetPrice($netpriceamount, $netpricebasisquantity, $netpricebasisquantityunitcode);
					$document->getDocumentPositionLineSummation($lineTotalAmount, $totalAllowanceChargeAmount);
					$document->getDocumentPositionQuantity($billedquantity, $billedquantityunitcode, $chargeFreeQuantity, $chargeFreeQuantityunitcode, $packageQuantity, $packageQuantityunitcode);
					?>
							<?php if($document->firstDocumentPositionNote()){ ?>
						<tr>
							<td>&nbsp;</td>
							<td colspan="5">
						<?php $document->getDocumentPositionNote($posnoteContent, $posnoteContentCode, $posnoteSubjectCode); ?>
			<?= $posnoteContent; ?>
							</td>
						</tr>
		<?php } while($document->nextDocumentPositionNote()); ?>
					<tr>
						<td class="posno"><?= $lineid; ?></td>
						<td class="posdesc"><?= $prodname . ($proddesc ? '<div class="prodDesc">' . nl2br(trim($proddesc)) . '</span>' : ''); ?></td>
						<td class="posqty"><?= $billedquantity; ?>&nbsp;<?= $transformer->transformUnit($billedquantityunitcode) ?></td>
						<td class="posunitprice"><?= $transformer->formatCurrency($netpriceamount, $invoiceCurrency); ?></td>
						<td class="poslinevat">
							<?php if($document->firstDocumentPositionTax()){
								$document->getDocumentPositionTax($categoryCode, $typeCode, $rateApplicablePercent, $calculatedAmount, $exemptionReason, $exemptionReasonCode);
								?>
			<?= $transformer->formatCurrency($rateApplicablePercent, '%'); ?>
					<?php } else { ?>
								&nbsp;
					<?php } ?>
						</td>
						<td class="poslineamount"><?= $transformer->formatCurrency($lineTotalAmount, $invoiceCurrency); ?></td>
					</tr>
		<?php
		if($document->firstDocumentPositionGrossPriceAllowanceCharge()){
			do{
				$document->getDocumentPositionGrossPrice($grossAmount, $grossBasisQuantity, $grossBasisQuantityUnitCode);
				$document->getDocumentPositionGrossPriceAllowanceCharge($actualAmount, $isCharge, $calculationPercent, $basisAmount, $reason, $taxTypeCode, $taxCategoryCode, $rateApplicablePercent, $sequence, $basisQuantity, $basisQuantityUnitCode, $reasonCode);
				?>
							<tr>
								<td class="posno">&nbsp;</td>
								<td class="posdesc bold italic"><?= ($isCharge ? $transformer->getString('Charge') : $transformer->getString('Allowance')); ?></td>
								<td class="posqty">&nbsp;</td>
								<td class="posunitprice italic"><?= $transformer->formatCurrency($actualAmount, $invoiceCurrency); ?> (<?= $transformer->formatCurrency($grossAmount, $invoiceCurrency); ?>)</td>
							</tr>
			<?php
			}while($document->nextDocumentPositionGrossPriceAllowanceCharge());
		}
	}while($document->nextDocumentPosition());
}
?>
		</tbody></table>
	<!--
			Allowance/Charge
	-->
	<div class="parallel space">
<?php $document->getDocumentSummation($grandTotalAmount, $duePayableAmount, $lineTotalAmount, $chargeTotalAmount, $allowanceTotalAmount, $taxBasisTotalAmount, $taxTotalAmount, $roundingAmount, $totalPrepaidAmount); ?>
		<!--
				VAT Summation
		-->

<?php if($document->firstDocumentTax()){ ?>
			<div><h2><?= $transformer->getString('VAT Breakdown'); ?></h2>
				<table class="space">
					<thead>
						<tr>
							<th><?= $transformer->getString('VAT'); ?></th>
							<th><?= $transformer->getString('Net'); ?></th>
							<th><?= $transformer->getString('Tax'); ?></th>
							<th><?= $transformer->getString('Gross'); ?></th>
						</tr></thead>
					<tbody>
						<?php
						$isfirsttax = true;
						$sumbasisamount = 0.0;
						do{
							$document->getDocumentTax($categoryCode, $typeCode, $basisAmount, $calculatedAmount, $rateApplicablePercent, $exemptionReason, $exemptionReasonCode, $lineTotalBasisAmount, $allowanceChargeBasisAmount, $taxPointDate, $dueDateTypeCode);
							?>
							<tr>
								<td class="totalname<?= $isfirsttax ? ' space' : '' ?>"><?= $transformer->formatCurrency($rateApplicablePercent, '%'); ?></td>
								<td class="totalvalue<?= $isfirsttax ? ' space' : '' ?>"><?= $transformer->formatCurrency($basisAmount, $invoiceCurrency); ?></td>
								<td class="totalvalue bold<?= $isfirsttax ? ' space' : '' ?>"><?= $transformer->formatCurrency($calculatedAmount, $invoiceCurrency); ?></td>
								<td class="totalvalue bold<?= $isfirsttax ? ' space' : '' ?>"><?= $transformer->formatCurrency($basisAmount + $calculatedAmount, $invoiceCurrency); ?></td>
							</tr>
				<?php
				$sumbasisamount = $sumbasisamount + $basisAmount;
				$isfirsttax = false;
			}while($document->nextDocumentTax());
			?>
						<tr>
							<td class="totalname"><?= $transformer->getString('Total'); ?></td>
							<td class="totalvalue"><?= $transformer->formatCurrency($sumbasisamount, $invoiceCurrency); ?></td>
							<td class="totalvalue bold"><?= $transformer->formatCurrency($taxTotalAmount, $invoiceCurrency); ?></td>
							<td class="totalvalue bold"><?= $transformer->formatCurrency($sumbasisamount + $taxTotalAmount, $invoiceCurrency); ?></td>
						</tr>
					</tbody></table>
			</div>
					<?php } ?>

<?php if($document->firstDocumentAllowanceCharge()){ ?>
			<div><h2><?= $transformer->getString('Allowance/Charge'); ?></h2>
				<table><tbody>
	<?php
	$isFirstDocumentAllowanceCharge = true;
	do{
		$document->getDocumentAllowanceCharge($actualAmount, $isCharge, $taxCategoryCode, $taxTypeCode, $rateApplicablePercent, $sequence, $calculationPercent, $basisAmount, $basisQuantity, $basisQuantityUnitCode, $reasonCode, $reason);
		?>
							<tr>
								<td class="<?= $isFirstDocumentAllowanceCharge ? 'space' : ''; ?> totalname"><?= $reason ? $reason : ($isCharge ? $transformer->getString('Charge') : $transformer->getString('Allowance')); ?></td>
								<td class="<?= $isFirstDocumentAllowanceCharge ? 'space' : ''; ?> totalvalue"><?= $transformer->formatCurrency($basisAmount, $invoiceCurrency); ?></td>
								<td class="<?= $isFirstDocumentAllowanceCharge ? 'space' : ''; ?> totalvalue bold"><?= $transformer->formatCurrency($actualAmount, $invoiceCurrency); ?></td>
							</tr>
							<?php $isFirstDocumentAllowanceCharge = false;
						}while($document->nextDocumentAllowanceCharge());
						?>
					</tbody></table>
			</div><?php }
					?>

		<!--
				Summmation
		-->
		<div><h2><?= $transformer->getString('Totals'); ?></h2>
			<table><tbody>
					<tr>
						<td class="space totalname" colspan="2"><?= $transformer->getString('Net Total'); ?></td>
						<td class="space totalvalue"><?= $transformer->formatCurrency($lineTotalAmount, $invoiceCurrency); ?></td>
					</tr>
<?php if($chargeTotalAmount != 0){ ?>
						<tr>
							<td class="totalname" colspan="2"><?= $transformer->getString('Charge Total'); ?></td>
							<td class="totalvalue"><?= $transformer->formatCurrency($chargeTotalAmount, $invoiceCurrency); ?></td>
						</tr>
<?php }
if($allowanceTotalAmount != 0){
	?>
						<tr>
							<td class="totalname" colspan="2"><?= $transformer->getString('Allowance Total'); ?></td>
							<td class="totalvalue"><?= $transformer->formatCurrency($allowanceTotalAmount, $invoiceCurrency); ?></td>
						</tr>
<?php } ?>
					<tr>
						<td class="totalname" colspan="2"><?= $transformer->getString('Tax'); ?></td>
						<td class="totalvalue"><?= $transformer->formatCurrency($taxTotalAmount, $invoiceCurrency); ?></td>
					</tr>
					<tr>
						<td class="totalname bold" colspan="2"><?= $transformer->getString('Gross Total'); ?></td>
						<td class="totalvalue bold"><?= $transformer->formatCurrency($grandTotalAmount, $invoiceCurrency); ?></td>
					</tr>
					<tr>
						<td class="totalname bold" colspan="2"><?= $transformer->getString('Already paid'); ?></td>
						<td class="totalvalue bold"><?= $transformer->formatCurrency($totalPrepaidAmount, $invoiceCurrency); ?></td>
					</tr>
					<tr>
						<td class="totalname bold" colspan="2"><?= $transformer->getString('Amount to pay'); ?></td>
						<td class="totalvalue bold"><?= $transformer->formatCurrency($duePayableAmount, $invoiceCurrency); ?></td>
					</tr>
				</tbody></table>
		</div>
	</div>
	<!--
			Paymentterms
	-->
			<?php if($document->firstDocumentPaymentTerms() || $document->firstGetDocumentPaymentMeans()){ ?>
		<h2><?= $transformer->getString('Payment information'); ?></h2>
		<table><tbody>
				<?php if($document->firstDocumentPaymentTerms()){
					do{
						?>
						<?php
						$document->getDocumentPaymentTerm($description, $dueDate, $directDebitMandateID);
						echo
						($description ? '<tr><td></td><td>' . $description . '</td></tr>' : '') .
						($dueDate ? '<tr><td>' . $transformer->getString('Due date') . '</td><td>' . $transformer->formatDate($dueDate) . '</td></tr>' : '') .
						($directDebitMandateID ? '<tr><td>' . $transformer->getString('Mandate') . '</td><td>' . $directDebitMandateID . '</td></tr>' : '');
					}while($document->nextDocumentPaymentTerms());
				}
				?>
				<!--
						Paymentmeans
				-->
				<?php if($document->firstGetDocumentPaymentMeans()){
					do{
						?>
						<?php
						$document->getDocumentPaymentMeans($typeCode, $information, $cardType, $cardId, $cardHolderName, $buyerIban, $payeeIban, $payeeAccountName, $payeePropId, $payeeBic);
						$document->getDocumentGeneralPaymentInformation($creditorReferenceID, $paymentReference);
						$document->getDocumentPayee($payeeName, $payeeid, $payeeDescription);
						?>
				<?=
				'<tr><td>' . $transformer->getString('Payment') . '</td><td>' . $transformer->transformPayment($typeCode) . '<br/>' . $information . '</td></tr>';
				switch($typeCode){
					//SEPA Credit Transfer
					case \horstoeko\zugferd\codelists\ZugferdPaymentMeans::UNTDID_4461_30:
					case \horstoeko\zugferd\codelists\ZugferdPaymentMeans::UNTDID_4461_58:
						echo ($payeeName ? '<tr><td>' . $transformer->getString('Payee name') . '</td><td>' . $payeeName . '</td></tr>' : '') .
						($payeeAccountName ? '<tr><td>' . $transformer->getString('Account name') . '</td><td>' . $payeeAccountName . '</td></tr>' : '') .
						'<tr><td>IBAN</td><td>' . $payeeIban . '</td></tr>' .
						($payeeBic ? '<tr><td>BIC</td><td>' . $payeeBic . '</td></tr>' : '') .
						($paymentReference ? '<tr><td>' . $transformer->getString('Payment reference') . '</td><td>' . $paymentReference . '</td></tr>' : '');
						break;
					//SEPA Direct Debit
					case \horstoeko\zugferd\codelists\ZugferdPaymentMeans::UNTDID_4461_59:
						echo '<tr><td>' . $transformer->getString('Byer IBAN') . '</td><td>' . $buyerIban . '</td></tr>' .
						'<tr><td>' . $transformer->getString('Creditor reference') . '</td><td>' . $creditorReferenceID . '</td></tr>';
				}
				?>
			<?php
		}while($document->nextGetDocumentPaymentMeans());
	}
	?>
			</tbody>
		</table>
<?php } ?>

<?php
$document->getDocumentNotes($documentNotes);
if($documentNotes){
	echo '<h2>' . $transformer->getString('Notes') . '</h2><ul>';
	foreach($documentNotes as $documentNote){
		echo '<li>' . nl2br(trim($documentNote['content'])) . '</li>';
	}
	echo '</ul>';
}
?>
</body>
</html>
