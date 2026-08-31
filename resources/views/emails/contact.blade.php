<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de contact</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #72c8d3 0%, #3a494f 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .info-row {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #3a494f;
            margin-bottom: 5px;
        }
        .value {
            color: #555;
        }
        .message-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #72c8d3;
            margin-top: 10px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Nouveau Message de Contact</h1>
            <p style="margin: 10px 0 0 0;">VEHIX - Vehicle Manager Digital</p>
        </div>
        
        <div class="content">
            <p>Vous avez reçu un nouveau message via le formulaire de contact de VEHIX.</p>
            
            <div class="info-row">
                <div class="label">👤 Nom de l'expéditeur</div>
                <div class="value">{{ $contactData['name'] }}</div>
            </div>
            
            <div class="info-row">
                <div class="label">📧 Email de l'expéditeur</div>
                <div class="value">
                    <a href="mailto:{{ $contactData['email'] }}" style="color: #72c8d3; text-decoration: none;">
                        {{ $contactData['email'] }}
                    </a>
                </div>
            </div>
            
            <div class="info-row">
                <div class="label">📌 Sujet</div>
                <div class="value">{{ $contactData['subject'] }}</div>
            </div>
            
            <div class="info-row">
                <div class="label">💬 Message</div>
                <div class="message-box">
                    {{ $contactData['message'] }}
                </div>
            </div>
            
            <div style="margin-top: 30px; padding: 15px; background: #e8f4f8; border-radius: 8px; text-align: center;">
                <p style="margin: 0; color: #3a494f;">
                    <strong>💡 Astuce:</strong> Vous pouvez répondre directement en cliquant sur "Répondre" dans votre client email.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                <strong>VEHIX - Vehicle Manager Digital</strong>
            </p>
            <p style="margin: 0;">
                Ambohipo LOT VT 31 C Bis, 101 Antananarivo - Madagascar<br>
                +261 34 40 994 35 | hasinaandritina538@gmail.com
            </p>
            <p style="margin: 15px 0 0 0; color: #999;">
                © {{ date('Y') }} HASNREZIGA Informatique - Tous droits réservés
            </p>
        </div>
    </div>
</body>
</html>