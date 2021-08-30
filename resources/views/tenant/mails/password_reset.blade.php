<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield('title')</title>
  <style type="text/css">
    #outlook a {
      padding: 0;
    }

    body {
      font-family: 'Arial', 'Helvetica', sans-serif;
      background: white;
      width: 100% !important;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
      margin-top: 0px;
      margin-right: 0px;
      margin-bottom: 0px;
      margin-left: 0px;
      padding-top: 0px;
      padding-right: 0px;
      padding-bottom: 0px;
      padding-left: 0px;
    }

    .ExternalClass {
      width: 100%;
    }

    .ExternalClass * {
      line-height: 100%;
    }

    .ExternalClass,
    .ExternalClass p,
    .ExternalClass span,
    .ExternalClass font,
    .ExternalClass td,
    .ExternalClass div {
      line-height: 100%;
    }

    img {
      outline: none;
      text-decoration: none;
      -ms-interpolation-mode: bicubic;
    }

    a img {
      border: none;
    }

    .image-fix {
      display: block;
    }

    .unsub-text {
      mso-line-height-rule: exactly;
      font-family: 'Arial', 'Helvetica', sans-serif;
      font-size: 15px;
      line-height: 15px;
      font-weight: normal;
      text-decoration: none;
      color: #cbd0d6;
    }

    .unsub-text a {
      text-decoration: none;
      color: #cbd0d6;
    }

    .address-text {
      mso-line-height-rule: exactly;
      font-family: 'Arial', 'Helvetica', sans-serif;
      font-size: 15px;
      line-height: 15px;
      font-weight: normal;
      text-decoration: none;
      color: #cbd0d6;
    }

    .address-text a {
      text-decoration: none;
      color: #cbd0d6;
    }

    @media only screen and (max-width: 480px) {

      /*video blocks*/
      table[class="content-table"] {
        width: 320px !important;
      }

      table[class="mobile-frame"] {
        width: 280px !important;
        background-color: #f4f4f4 !important;
      }

      table[class="video-table"] {
        width: 240px !important;
        background-color: #f4f4f4 !important;
      }

      table[class="cc"] {
        background-color: white !important;
      }

      td[class="mobile-side-spacing"] {
        padding-top: 0px !important;
        padding-right: 20px !important;
        padding-bottom: 0px !important;
        padding-left: 20px !important;
      }

      td[class="video-subject"] {
        padding-top: 35px !important;
        padding-right: 30px !important;
        padding-bottom: 0px !important;
        padding-left: 30px !important;
        font-size: 20px !important;
        line-height: 23px !important;
      }

      td[class="video-description"] {
        padding-top: 15px !important;
        padding-right: 20px !important;
        padding-bottom: 0px !important;
        padding-left: 20px !important;
      }

      td[class="video-image"] {
        padding-top: 20px !important;
        padding-right: 0px !important;
        padding-bottom: 0px !important;
        padding-left: 0px !important;
      }

      td[class="video-image"] img {
        width: 240px !important;
      }

      td[class="video-cta"] {
        padding-top: 10px !important;
        padding-right: 0px !important;
        padding-bottom: 10px !important;
        padding-left: 0px !important;
        display: inline-block !important;
        text-align: center !important;
      }

      td[class="social-block"] {
        padding-top: 10px !important;
        padding-right: 0px !important;
        padding-bottom: 20px !important;
        padding-left: 0px !important;
        display: block !important;
      }

      td[class="social-spacer"] {
        padding-top: 0px !important;
        padding-right: 0px !important;
        padding-bottom: 0px !important;
        padding-left: 0px !important;
      }

      td[class="button-wrapper"] {
        width: 240px !important;
      }

      td[class="footer-block-l"] {
        display: block !important;
        width: 320px !important;
      }

      td[class="footer-block-r"] {
        padding-top: 30px !important;
        padding-right: 0px !important;
        padding-bottom: 0px !important;
        padding-left: 0px !important;
        display: block !important;
        width: 320px !important;
      }

      td[class="company-name"] {
        text-align: center !important;
      }

      td[class="address"] {
        text-align: center !important;
      }

      td[class="edit"] {
        text-align: center !important;
      }

      td[class="unsub"] {
        text-align: center !important;
      }

      *[class="is-mobile"] {
        display: block !important;
        max-height: none !important;
      }

      *[class="is-desktop"] {
        display: none;
        max-height: 0px;
        overflow: hidden;
      }
    }
  </style>
</head>

<body
  style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: white; font-family: 'Arial', 'Helvetica', sans-serif; margin-bottom: 0px; margin-left: 0px; margin-right: 0px; margin-top: 0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px; width: 100%;">
  <table bgcolor="#ffffff" width="100%" cellspacing="0" cellpadding="0" border="0" class="table-body"
    style="line-height: 100%; margin: 0; padding: 0; width: 100%;">
    <tr>
      <td align="center" style="border-collapse: collapse;">
        <table width="600" cellspacing="0" cellpadding="0" border="0" align="center" class="content-table">
          <tr>
            <td height="10" class="page-break" bgcolor="#ffffff"
              style="border-collapse: collapse; line-height: 10px; mso-line-height-rule: exactly;">
              <br style="visibility: hidden;">
            </td>
          </tr>
          <tr>
            <td style="border-collapse: collapse;">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="mobile-side-spacing" style="border-collapse: collapse;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr height="100">
                        <td valign="top" align="center" class="intro-text"
                          style="border-collapse: collapse;">
                          <br>
                            @if($company->logo)
                                <img src="{{ asset('storage/uploads/logos/' . $company->logo) }}" alt="Logo" width="220" />
                            @else
                                <img src="{{asset('logo/700x300.jpg')}}" alt="Logo" width="220" />
                            @endif
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td class="mobile-side-spacing" style="border-collapse: collapse;">
                    <br><br>
                    <h1 style="font-size: 18px;">¡Hola!</h1>
                    <p style="line-height: 1.4;">Recibió este correo electrónico porque recibimos una solicitud de restablecimiento de contraseña para su cuenta.</p>
                    <br>
                    <center>
                        <a style="background-color: #080301; padding: 10px 24px; color: #FFFFFF; text-decoration: none;" href="{{ $url }}">Restablecer contraseña</a>
                    </center>
                    <br>
                    <br>
                    <br>
                    <p style="font-size: 14px;">Este enlace de restablecimiento de contraseña caducará en 60 minutos.</p>
                    <br>
                    <br>
                    <p style="font-size: 14px;">Si no solicitó un restablecimiento de contraseña, no se requiere ninguna otra acción.</p>
                    <br>
                    <br>
                    <br>
                    <p style="color: #363945; font-size: 12px; line-height: 1.3;">Si tiene problemas para hacer clic en el botón "Restablecer contraseña", copie y pegue la siguiente URL en su navegador web: {{ $url }}</p>
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="mobile-side-spacing" style="border-collapse: collapse;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td valign="top" align="left" class="conclusion"
                          style="border-collapse: collapse; color: #363945; font-family: 'Arial', 'Helvetica', sans-serif; font-size: 16px; font-weight: normal; line-height: 21px; mso-line-height-rule: exactly; text-decoration: none;">
                          <br><br>
                          Saludos,<br><br>{{ $company->trade_name }}<br><br>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td height="10" class="page-break" bgcolor="#ffffff"
              style="border-collapse: collapse; line-height: 10px; mso-line-height-rule: exactly;">
              <br style="visibility: hidden;">
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

</body>

</html>
