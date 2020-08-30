# goAPI

<p>The GOautodial APIs allow you to perform operations and actions within GOautodial from external applications. This allows GOautodial to integrate seamlessly with third party software and custom code.</p>
<p>Access to the APIs are via HTTPS and authentication is done via username and password.</p>
<p>To install (need to have a working GOautodial v4):</p>
<pre>
cd /var/www/html
git clone https://github.com/goautodial/goAPIv2
</pre>
<p>Documentation (incomplete): https://drive.google.com/drive/folders/1ERrp_QdSVBsIpPKzoeU2hNQikCYSKJH0?usp=sharing.</p><br>
<p>Sample PHP code to create campaign:
  
  	$postfields = array(
      'goUser' => $goUser,
      'goPass' => $goPass,
      'goAction' => ‘goAddCampaign’,
      'session_user' => $session_user,
      'responsetype' => 'json',
      ‘campaign_id’ => ‘12231977’,
      ‘campaign_name’ => ‘Testcampaign’,
      ‘campaign_type’ => ‘outbound’, 
      ‘dial_prefix’ => ‘CUSTOM’, 
      ‘custom_prefix’ => ‘9’,
      ‘dial_method’ => ‘MANUAL’, 
      ‘auto_dial_level’ => ‘OFF’, 
      ‘campaign_recording’ => ‘NEVER’, 
      ‘answering_machine_detection’ => ‘8369’, 
      ‘session_user’ => ‘$session_user’, 
      ‘user_group’ => ‘ADMIN’
      ‘active’ => ‘Y’
	  );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
    $data = curl_exec($ch);
    curl_close($ch);
  
</p>
<p>Sample return:

    Success
    HTTP 200:
    {result: "success"}

    Failure
    HTTP 400:
    {
    "code" : "40001"
    "result" : "Error: Missing required parameters"
    }
</p>
<p>Curl utility:</p>
<pre>
curl 'https://DOMAINNAME/goAPIv2/goCampaigns/goAPI.php?goAction=goAddCampaign&goUser=goAPIuser&goPass=ENCRYPTED_PASSWORD&responsetype=json&campaign_id=12231977&campaign_name=CAMPAIGN_NAME&campaign_type=outbound&dial_prefix=CUSTOM&custom_prefix=9&dial_method=MANUAL&auto_dial_level=OFF&campaign_recording=NEVER&answering_machine_detection=8369&session_user=admin&user_group=ADMIN'
</pre>

The encrypted password can be derived from the pass_hash column in the vicidial_users table under the asterisk database. Or use /var/www/html/bin/pass_hasher.php to generate the encrypted password.

## OpenAPI Spec

There is also a work-in-progress openapi spec composed by @God-Zalo which can be used for
code-generating 
[client sdks in different languages](https://github.com/OpenAPITools/openapi-generator):

- https://app.swaggerhub.com/apis/godzalo/goautodial-users/0.1.3
