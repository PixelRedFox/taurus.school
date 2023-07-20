<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $urlSite = $_SERVER['HTTP_HOST'];
    $pageTitle = $_POST["page_request"];
    $dateQuery = date("Y-m-d");
    $utmParams = array(
        "utm_source" => $_POST["utm_source"] ?? "",
        "utm_medium" => $_POST["utm_medium"] ?? "",
        "utm_campaign" => $_POST["utm_campaign"] ?? "",
        "utm_term" => $_POST["utm_term"] ?? "",
        "utm_content" => $_POST["utm_content"] ?? ""
    );

    $bot_token = '5874335640:AAEmriZ0z1gS70_NRXVEmOPwn_U_S-zaxEk';
    $chat_id = '-1001945580350';

    $message = "Ім'я: $name\nТелефон: $phone\n\nДоменне ім'я сайту: $urlSite\nНайменування сторінки: $pageTitle\nДата запиту: $dateQuery\n\nUTM-параметри:\n";
    foreach ($utmParams as $key => $value) {
        $message .= "$key: $value\n";
    }

    $telegram_url = "https://api.telegram.org/bot$bot_token/sendMessage";
    $telegram_params = [
        'chat_id' => $chat_id,
        'text' => $message
    ];

    $ch = curl_init($telegram_url);
    curl_setopt_array($ch, [
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $telegram_params,
        CURLOPT_RETURNTRANSFER => true
    ]);
    $result = curl_exec($ch);
    curl_close($ch);

    $url = "https://openapi.keycrm.app/v1/pipelines/cards";
    $data = [
        "title" => "Заявка з сайту $urlSite від $dateQuery з сторінки $pageTitle",
        "contact" => [
            "full_name" => $name,
            "email" => "",
            "phone" => $phone,
            "client_id" => ""
        ],
        "utm_source" => $utmParams["utm_source"],
        "utm_medium" => $utmParams["utm_medium"],
        "utm_campaign" => $utmParams["utm_campaign"],
        "utm_term" => $utmParams["utm_term"],
        "utm_content" => $utmParams["utm_content"]
    ];

    $headers = [
        "Content-type: application/json",
        "Authorization: Bearer MjBmOGFmNzhjYzI2NjY1Zjc0OTVhMjMxOThkNjI5YmFmN2VlNWYxMg"
    ];

    $options = [
        "http" => [
            "header" => implode("\r\n", $headers),
            "method" => "POST",
            "content" => json_encode($data)
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    // Відправка запиту на адресу https://script.google.com/macros/s/AKfycbyA6gVBYOXbS6SffQt3T5AGT8tjbQbdJIuixq2wqCwbzkMFg_SATRwMCPvJ0BG_MeNK/exec
    $google_url = "https://script.google.com/macros/s/AKfycbyA6gVBYOXbS6SffQt3T5AGT8tjbQbdJIuixq2wqCwbzkMFg_SATRwMCPvJ0BG_MeNK/exec";
    $google_params = [
        "name" => $name,
        "phone" => $phone,
        "utm_source" => $utmParams["utm_source"],
        "utm_medium" => $utmParams["utm_medium"],
        "utm_campaign" => $utmParams["utm_campaign"],
        "utm_content" => $utmParams["utm_content"],
        "utm_term" => $utmParams["utm_term"],
        "page_request" => $urlSite . $pageTitle
    ];

    $ch = curl_init($google_url);
    curl_setopt_array($ch, [
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => http_build_query($google_params),
        CURLOPT_RETURNTRANSFER => true
    ]);
    $result = curl_exec($ch);
    curl_close($ch);

    if ($result !== false) {
        header("Location: thanks.html");
        exit();
    } else {
        echo "Помилка при відправленні запиту.";
    }
}
?>
