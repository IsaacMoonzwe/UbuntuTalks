<table tyle="border: 1px solid #ddd; border-collapse: collapse" cellspacing="0" cellpadding="0" border="0">
    <tbody>
        <?php
        switch ($pmethodCode) {
            case PaymentMethods::BANK_PAYOUT_KEY:
                ?>
                <tr>
                    <td style=" padding: 10px;font-size: 13px;border: 1px solid #ddd;color: #333;font-weight: bold;" width="153"><?php echo Label::getLabel('LBL_Bank_Name'); ?> </td>
                    <td style=" padding: 10px;font-size: 13px;border: 1px solid #ddd;color: #333;font-weight: bold;" width="620"> <?php echo $data['ub_bank_name']; ?> </td>
                </tr>
                <tr>
                    <td style=" padding: 10px;font-size: 13px;border: 1px solid #ddd;color: #333;font-weight: bold;" width="153"><?php echo Label::getLabel('LBL_Account_Holder_name'); ?> </td>
                    <td style=" padding: 10px;font-size: 13px;border: 1px solid #ddd;color: #333;font-weight: bold;" width="620"> <?php echo $data['ub_account_holder_name']; ?> </td>
                </tr>
                <tr>
                    <td style=" padding: 10px;font-size: 13px;border: 1px solid #ddd;color: #333;font-weight: bold;" width="153"><?php echo Label::getLabel('LBL_Account_Number'); ?> </td>
                    <td style=" padding: 10px;font-size: 13px;border: 1px solid #ddd;color: #333;font-weight: bold;" width="620"> <?php echo $data['ub_account_number']; ?> </td>
                </tr>
                <tr>
                    <td style=" padding: 10px;font-size: 13px;border: 1px solid #ddd;color: #333;font-weight: bold;" width="153"><?php echo Label::getLabel('LBL_Swift_Code'); ?> </td>
                    <td style=" padding: 10px;font-size: 13px;border: 1px solid #ddd;color: #333;font-weight: bold;" width="620"> <?php echo $data['ub_ifsc_swift_code']; ?> </td>
                </tr>
                <tr>
                    <td style=" padding: 10px;font-size: 13px;border: 1px solid #ddd;color: #333;font-weight: bold;" width="153"><?php echo Label::getLabel('LBL_Bank_Address'); ?> </td>
                    <td style=" padding: 10px;font-size: 13px;border: 1px solid #ddd;color: #333;font-weight: bold;" width="620"> <?php echo $data['ub_bank_address']; ?> </td>
                </tr>
                <?php
                break;
            default:
                ?>
            <td style=" padding: 10px;font-size: 13px;border: 1px solid #ddd;color: #333;font-weight: bold;" width="153"><?php echo Label::getLabel('LBL_Paypal_Email_address'); ?> </td>
            <td style=" padding: 10px;font-size: 13px;border: 1px solid #ddd;color: #333;font-weight: bold;" width="620"> <?php echo $data['ub_paypal_email_address']; ?> </td>
        </tr>
        <?php
        break;
}
?>
</tbody>
</tabale>