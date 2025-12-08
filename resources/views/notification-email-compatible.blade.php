<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Pago exitoso - {{ config('app.name') }}</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, Helvetica, sans-serif !important;}
    </style>
    <![endif]-->
    <style type="text/css">
        /* Reset styles */
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
        }
        table {
            border-spacing: 0;
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        table td {
            border-collapse: collapse;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
        }
        a {
            text-decoration: none;
        }
        /* Button hover - only works in some clients */
        @media screen {
            .btn-pagar:hover {
                background-color: #477c87 !important;
            }
        }
    </style>
</head>

<body style="margin: 0; padding: 0; width: 100%; background-color: #ffffff; -webkit-font-smoothing: antialiased;">

    <!-- Wrapper Table -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff;">
        <tr>
            <td align="center" valign="top">

                <!-- Main Container -->
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%;">

                    <!-- Header -->
                    <tr>
                        <td align="center" valign="middle" style="background-color: #12131C; padding: 48px 0;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        <img src="{{ config('app.logo') }}" alt="{{ config('app.name') }}" width="100" height="100" style="display: block; border-radius: 50%; border: 0; width: 100px; height: 100px;" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="height: 1px; background-color: #000000; font-size: 1px; line-height: 1px;">&nbsp;</td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td align="center" valign="top" style="background-color: #ffffff; padding: 50px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <!-- Title -->
                                <tr>
                                    <td align="center" style="padding-bottom: 24px;">
                                        <h1 style="margin: 0; font-family: 'Montserrat', Arial, Helvetica, sans-serif; font-size: 32px; font-weight: 100; color: #2F3541; letter-spacing: 2px;">Pago exitoso</h1>
                                    </td>
                                </tr>

                                <!-- Message -->
                                <tr>
                                    <td align="center" style="padding-bottom: 32px;">
                                        <p style="margin: 0; font-family: 'PT Serif', Georgia, Times, 'Times New Roman', serif; font-size: 16px; font-weight: 400; color: #2F3541; line-height: 24px;">
                                            El pago ha sido recibido con éxito. Ahora puede descargar el archivo en el enlace de abajo.
                                        </p>
                                    </td>
                                </tr>

                                <!-- Button -->
                                <tr>
                                    <td align="center" style="padding-bottom: 32px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td align="center" style="border-radius: 8px; background-color: #A4CBD3;">
                                                    <a href="{{ $file_url }}" target="_blank" class="btn-pagar" style="display: inline-block; padding: 12px 28px; font-family: 'Montserrat', Arial, Helvetica, sans-serif; font-size: 16px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 8px; background-color: #A4CBD3; mso-padding-alt: 0;">
                                                        <!--[if mso]>
                                                        <i style="letter-spacing: 28px; mso-font-width: -100%; mso-text-raise: 24pt;">&nbsp;</i>
                                                        <![endif]-->
                                                        <span style="mso-text-raise: 12pt;">DESCARGAR ARCHIVO</span>
                                                        <!--[if mso]>
                                                        <i style="letter-spacing: 28px; mso-font-width: -100%;">&nbsp;</i>
                                                        <![endif]-->
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Signature -->
                                <tr>
                                    <td align="center" style="padding-bottom: 0;">
                                        <p style="margin: 0; font-family: 'PT Serif', Georgia, Times, 'Times New Roman', serif; font-size: 16px; font-weight: 400; color: #2F3541; line-height: 24px;">
                                            Un saludo,<br />
                                            Equipo de {{ config('app.name') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" valign="top" style="background-color: #12131C; padding: 32px 48px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <!-- Divider -->
                                <tr>
                                    <td style="padding-bottom: 24px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="height: 1px; background-color: #2F3541; font-size: 1px; line-height: 1px;">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Logo -->
                                <tr>
                                    <td align="center" style="padding-bottom: 16px;">
                                        <img src="{{ config('app.logo') }}" alt="{{ config('app.name') }}" width="100" height="100" style="display: block; border-radius: 50%; border: 0; width: 100px; height: 100px;" />
                                    </td>
                                </tr>

                                <!-- Copyright -->
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0; font-family: 'Montserrat', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 600; color: #ffffff; line-height: 20px;">
                                            &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
                <!-- End Main Container -->

            </td>
        </tr>
    </table>
    <!-- End Wrapper Table -->

</body>

</html>
