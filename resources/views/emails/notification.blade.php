<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office" lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="x-apple-disable-message-reformatting" />
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no" />
    <meta name="color-scheme" content="dark" />
    <meta name="supported-color-schemes" content="dark" />
    <title>{{ $titulo }}</title>
    <!--[if mso]>
<xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
<style>table,td,div,p,a,h1,h2,h3{font-family:Arial,Helvetica,sans-serif !important;}</style>
<![endif]-->
    <style type="text/css">
        /*
    Paleta (hex en línea, los correos no soportan variables CSS):
    bg #000000 | bg2 #1c1917 | fg #f5f0e8 | fg-muted #8a8078 | fg-dim #6b6560
    primary #f5a623 | primary-dark #d4900e | border #2a2520 | red #e53e3e
  */
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background: #000000;
        }

        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            border-collapse: collapse;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
            display: block;
        }

        a {
            text-decoration: none;
        }

        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        u+#body a {
            color: inherit;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #d4900e !important;
            border-color: #d4900e !important;
        }

        @media screen and (max-width:620px) {
            .container {
                width: 100% !important;
            }

            .px {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }

            .h1 {
                font-size: 26px !important;
                line-height: 32px !important;
            }

            .full-btn {
                display: block !important;
                width: 100% !important;
            }
        }
    </style>
</head>

<body id="body" style="margin:0;padding:0;background-color:#000000;" bgcolor="#000000">

    <!-- Texto de vista previa (preheader) -->
    <div
        style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;color:#000000;">
        {{ $titulo }} - {{ $mensaje }}
        &#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;
    </div>

    <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#000000"
        style="background-color:#000000;">
        <tr>
            <td align="center" style="padding:24px 12px;">

                <!--[if mso]><table role="presentation" width="600" align="center" border="0" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
                <table role="presentation" class="container" width="600" border="0" cellpadding="0"
                    cellspacing="0" style="width:600px;max-width:600px;">

                    <!-- ENCABEZADO: logo + nombre de la empresa -->
                    <tr>
                        <td align="center" bgcolor="#1c1917"
                            style="background-color:#1c1917;border:1px solid #2a2520;border-bottom:3px solid #f5a623;padding:28px 32px;">
                            <a href="{{ config('app.url') }}" target="_blank" style="text-decoration:none;">
                                <img src="{{ config('app.logo') }}" width="64" height="64" alt="{{ config('app.name') }}"
                                    style="display:block;margin:0 auto;width:64px;height:64px;border:0;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#f5a623;" />
                            </a>
                            <div
                                style="padding-top:12px;font-family:Arial,Helvetica,sans-serif;font-size:20px;line-height:26px;font-weight:bold;letter-spacing:1px;color:#f5f0e8;">
                                {{ config('app.name') }}
                            </div>
                        </td>
                    </tr>

                    <!-- CUERPO -->
                    <tr>
                        <td bgcolor="#000000"
                            style="background-color:#000000;border-left:1px solid #2a2520;border-right:1px solid #2a2520;">
                            <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0">

                                <!-- Título -->
                                <tr>
                                    <td class="px" align="center" style="padding:40px 40px 0 40px;">
                                        <h1 class="h1"
                                            style="margin:0;font-family:Georgia,'Times New Roman',serif;font-size:32px;line-height:40px;font-weight:bold;color:#f5f0e8;text-align:center;">
                                            {{ $titulo }}
                                        </h1>
                                    </td>
                                </tr>

                                <!-- Saludo -->
                                <tr>
                                    <td class="px" align="center"
                                        style="padding:20px 40px 0 40px;font-family:Arial,Helvetica,sans-serif;font-size:17px;line-height:26px;font-weight:bold;color:#f5a623;text-align:center;">
                                        Hola {{ $nombre }},
                                    </td>
                                </tr>

                                <!-- Mensaje (centrado). Puedes usar varios párrafos -->
                                <tr>
                                    <td class="px" align="center"
                                        style="padding:12px 40px 20px 40px;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:26px;color:#f5f0e8;text-align:center;">
                                        {{ $mensaje }}
                                    </td>
                                </tr>

                                <!-- ============ BOTÓN OPCIONAL: borra este bloque si no lo necesitas ============ -->
                                @if(!empty($boton_url) && !empty($boton_texto))
                                <tr>
                                    <td class="px" align="center" style="padding:32px 40px 0 40px;">
                                        <!--[if mso]>
              <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $boton_url }}" style="height:48px;v-text-anchor:middle;width:240px;" arcsize="8%" strokecolor="#f5a623" fillcolor="#f5a623">
                <w:anchorlock/>
                <center style="color:#000000;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;">{{ $boton_texto }}</center>
              </v:roundrect>
              <![endif]-->
                                        <!--[if !mso]><!-- -->
                                        <a class="btn full-btn" href="{{ $boton_url }}" target="_blank"
                                            style="display:inline-block;background-color:#f5a623;border:1px solid #f5a623;border-radius:4px;color:#000000;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:46px;font-weight:bold;text-align:center;text-decoration:none;width:240px;-webkit-text-size-adjust:none;mso-hide:all;">{{ $boton_texto }}</a>
                                        <!--<![endif]-->
                                    </td>
                                </tr>
                                <!-- Enlace alternativo (útil para recuperar contraseña o verificar cuenta). Borra si no aplica -->
                                <tr>
                                    <td class="px" align="center"
                                        style="padding:20px 40px 0 40px;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:20px;color:#8a8078;text-align:center;">
                                        Si el botón no funciona, copia y pega este enlace en tu navegador:<br />
                                        <a href="{{ $boton_url }}" target="_blank"
                                            style="color:#f5a623;text-decoration:underline;word-break:break-all;">{{ $boton_url }}</a>
                                    </td>
                                </tr>
                                @endif
                                <!-- ============ FIN DEL BLOQUE OPCIONAL ============ -->

                                @if (!empty($nota))
                                <tr>
                                    <td class="px" align="center" style="padding:28px 40px 40px 40px;">
                                        <table role="presentation" width="100%" border="0" cellpadding="0"
                                            cellspacing="0" bgcolor="#1c1917"
                                            style="background-color:#1c1917;border:1px solid #2a2520;">
                                            <tr>
                                                <td align="center"
                                                    style="padding:14px 20px;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:20px;color:#8a8078;text-align:center;">
                                                    {{ $nota }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <!-- PIE DE PÁGINA -->
                    <tr>
                        <td align="center" bgcolor="#1c1917"
                            style="background-color:#1c1917;border:1px solid #2a2520;border-top:1px solid #2a2520;padding:28px 32px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                align="center">
                                <tr>
                                    <td align="center"
                                        style="font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:22px;color:#8a8078;text-align:center;">
                                        <a href="{{ config('app.url') }}" target="_blank"
                                            style="color:#f5f0e8;text-decoration:underline;">Sitio web</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center"
                                        style="padding-top:16px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:20px;color:#8a8078;text-align:center;">
                                        Este mensaje se envió a {{ $email }}.<br />
                                        Si no esperabas este correo, puedes ignorarlo.
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center"
                                        style="padding-top:12px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:20px;color:#6b6560;text-align:center;">
                                        &copy; {{ now()->year }} {{ config('app.name') }}. Todos los derechos
                                        reservados.<br />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
                <!--[if mso]></td></tr></table><![endif]-->

            </td>
        </tr>
    </table>

</body>

</html>
