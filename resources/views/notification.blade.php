<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Martel:wght@200;300;400;600;700;800;900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap");
        @import url("https://fonts.googleapis.com/css2?family=Martel:wght@200;300;400;600;700;800;900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap");
        @import url('https://fonts.googleapis.com/css2?family=Montserrat+Alternates:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        /* fonts */

        .montserrat-alternates-thin {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 100;
            font-style: normal;
        }

        .montserrat-alternates-extralight {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 200;
            font-style: normal;
        }

        .montserrat-alternates-light {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 300;
            font-style: normal;
        }

        .montserrat-alternates-regular {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        .montserrat-alternates-medium {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 500;
            font-style: normal;
        }

        .montserrat-alternates-semibold {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 600;
            font-style: normal;
        }

        .montserrat-alternates-bold {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 700;
            font-style: normal;
        }

        .montserrat-alternates-extrabold {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 800;
            font-style: normal;
        }

        .montserrat-alternates-black {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 900;
            font-style: normal;
        }

        .montserrat-alternates-thin-italic {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 100;
            font-style: italic;
        }

        .montserrat-alternates-extralight-italic {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 200;
            font-style: italic;
        }

        .montserrat-alternates-light-italic {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 300;
            font-style: italic;
        }

        .montserrat-alternates-regular-italic {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 400;
            font-style: italic;
        }

        .montserrat-alternates-medium-italic {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 500;
            font-style: italic;
        }

        .montserrat-alternates-semibold-italic {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 600;
            font-style: italic;
        }

        .montserrat-alternates-bold-italic {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 700;
            font-style: italic;
        }

        .montserrat-alternates-extrabold-italic {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 800;
            font-style: italic;
        }

        .montserrat-alternates-black-italic {
            font-family: "Montserrat Alternates", sans-serif;
            font-weight: 900;
            font-style: italic;
        }



        /* fonts */



        * {
            box-sizing: border-box;
        }

        body,
        html {
            padding: 0;
            margin: 0;
            height: 100vh;
        }

        :root {
            --Base-White: #000;
            --Primary-200: #FFF;
            --Primary-800: #FFF;
            --Primary-600: #FFF;
        }

        main {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
            /*  width: 37.5rem; */
            background: var(--Base-White);
            width: 100%;
            height: 100%;
        }

        div.header {
            display: flex;
            padding: 3rem 0rem;
            width: 100%;
            text-align: center;
            gap: 0.5rem;
            border-bottom: 1px solid var(--Primary-200);
            background: var(--Primary-800);
        }

        h1 {
            color: var(--Primary-600);
            font-family: "PT Serif";
            font-size: 1.875rem;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        p {
            color: var(--Primary-600, #2F3541);
            font-family: "PT Serif";
            font-size: 1rem;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .content span {
            color: var(--Primary-600, #2F3541);
            font-size: 1rem;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        h2,
        h1 {
            color: var(--Primary-600, #2F3541);
            font-family: "PT Serif";
            font-size: 2rem;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            margin-top: 0.75rem;
            letter-spacing: 0.2rem;
        }

        .content {
            padding: 0rem 2rem;
            flex-grow: 1;
        }

        footer {
            background: var(--Primary-800, #2F3541);
            width: 100%;
        }

        footer p {
            color: var(--Primary-300, #2F3541);
            text-align: center;
            padding: 0rem 3rem;

            /* Text sm/Semibold */
            font-family: "Montserrat";
            font-size: 0.875rem;
            font-style: normal;
            font-weight: 600;
            line-height: 1.25rem;
            /* 142.857% */
        }

        .links {
            display: flex;
            align-items: flex-start;
            gap: 2rem;
        }

        .final {
            padding-top: 2rem;
            border-top: 1px solid var(--Primary-600, #2F3541);
        }

        .logo-footer {
            text-align: center;
        }

        .final span {
            color: var(--Primary-300, #2F3541);
            /* Text md/Regular */
            font-family: "Montserrat";
            font-size: 1rem;
            font-style: normal;
            font-weight: 400;
            line-height: 1.5rem;
            /* 150% */
        }

        .logo-header {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 1rem;
            margin: auto;
        }

        .text-center {
            text-align: center
        }

        .content h1 {
            letter-spacing: normal !important;
        }

        .btn-pagar {
            background-color: #A4CBD3;
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white !important;
            text-decoration: none;
            display: inline-block;
            font-family: "Montserrat", sans-serif;
            text-align: center;
        }

        .btn-pagar:hover {
            background-color: #477c87;
            /* Verde más oscuro en hover */
        }

        .btn-pagar:active {
            background-color: #008f76;
            /* Más oscuro al hacer clic */
            transform: scale(0.98);
        }
    </style>
</head>


<body>
    <main>
        <div class="header" style="background-color: #12131C !important">
            <div class="logo-header">
                <img src="{{ asset('assets/img/favicon/icon.jpeg') }}" style="border-radius: 50%" alt="logo" width="100" height="100">
            </div>
        </div>
        <div class="content">
            <h1 style="padding-top: 50px;letter-spacing: 2px" class="montserrat-alternates-thin">Pago exitoso</h1>
            <p class="montserrat-alternates-regular">
                El pago ha sido recibido con éxito. Ahora puede descargar el archivo en el enlace de abajo.
            </p>
            <p class="montserrat-alternates-regular" style="display:flex; justify-content: center">
                <a class="btn-pagar" href="{{ $file_url }}">DESCARGAR ARCHIVO</a>
            </p>
            <p style="padding-bottom: 50px;" class="montserrat-alternates-regular">
                Un saludo,<br />
                Equipo de {{ config('app.name') }}
            </p>
        </div>
        <footer style="background-color: #12131C !important">
            <div class="final">
                <div class="logo-footer">
                    <img src="{{ asset('assets/img/favicon/icon.jpeg') }}" style="border-radius: 50%" alt="logo" width="100" height="100">
                </div>
                <p class="montserrat-alternates-regular" style="font-size: 18px;color:white"> © {{ date('Y') }}
                    {{ config('app.name') }}. Todos
                    los derechos reservados </p>
            </div>
        </footer>
    </main>
</body>

</html>
