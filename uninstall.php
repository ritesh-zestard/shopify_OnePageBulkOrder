<?php

$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$follow_up_headers = 'From: Zestard Technologies <shilpi@zestard.com>'. "\r\n";
$follow_up_headers .= 'MIME-Version: 1.0' . "\r\n";
$follow_up_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";


$json = '' . file_get_contents('php://input') . '';

$result = json_decode($json);

$connection = new mysqli("localhost", "anujdala_shopify", "5bSdT0rmiU*a", "anujdala_bulkorder_demo_new");

$delete_shop = "DELETE FROM usersettings WHERE store_name= '" . $result->myshopify_domain . "'";

$connection->query($delete_shop);

//for the follow up mail
$owner_name = $result->shop_owner;
$app_name = "One Page Bulk Order Demo";
$uninstallation_follow_up_msg ='<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @import url("https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i");
        @media only screen and (max-width:599px) {
            table {
                width: 100% !important;
            }
        }
        
        @media only screen and (max-width:412px) {
            h2 {
                font-size: 20px;
            }
            p {
                font-size: 13px;
            }
            .easy-donation-icon img {
                width: 120px;
            }
        }
    </style>

</head>

<body style="background: #f4f4f4; padding-top: 57px; padding-bottom: 57px;">
    <table class="main" border="0" cellspacing="0" cellpadding="0" width="600px" align="center" style="border: 1px solid #e6e6e6; background:#fff; ">
        <tbody>
            <tr>
                <td style="padding: 30px 30px 10px 30px;" class="review-content">
                    <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px; line-height: 25px; margin-top: 0px;"><b>Hi '.$owner_name.'</b>,</p>
                    <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">You have Un-installed Shopify Application - "'.$app_name.'" from your store.</p>
                    <p style="font-family: \'Helvetica\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">If you have faced any issue with the application in terms of functional part or design related issues, we can assist you to resolve it for you.</p>

                    <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">To provide better solution & support for all the Future Installations, your comments would be appreciated to improve the application.</p>
                    <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">Looking forward to understanding your business need with regards to our existing application features.</p>


                </td>
            </tr>

            <tr>
                <td style="padding: 20px 30px 30px 30px;">

                    <br>
                    <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 26px; margin-bottom:0px;">Thanks,<br>Zestard Support</p>
                </td>
            </tr>

        </tbody>
    </table>
</body>';
mail($result->email, "Zestard Application :: One Page Bulk Order", $uninstallation_follow_up_msg, $follow_up_headers);
mail("rdesoza98@gmail.com", "Zestard Application :: One Page Bulk Order", $uninstallation_follow_up_msg, $follow_up_headers);

$msg = '<table>
                <tr>
                    <th>Shop Name</th>
                    <td>' . $result->name . '</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>' . $result->email . '</td>
                </tr>
                <tr>
                    <th>Domain</th>
                    <td>' . $result->myshopify_domain . '</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>' . $result->phone . '</td>
                </tr>
                <tr>
                    <th>Shop Owner</th>
                    <td>' . $result->shop_owner . '</td>
                </tr>
                <tr>
                    <th>Country</th>
                    <td>' . $result->country_name . '</td>
                </tr>
                <tr>
                    <th>Plan</th>
                    <td>' . $result->plan_display_name . '</td>
                </tr>
              </table>';
$store_details = "SELECT dev_store_name FROM development_stores WHERE dev_store_name = '" . $result->myshopify_domain . "'";
$development_store = $connection->query($store_details);
$fetchObject = $development_store->fetch_object();

if (count($fetchObject) == 0) {
    mail("support@zestard.com", "One Page Bulk Order App Removed", $msg, $headers);    
    
}
?>