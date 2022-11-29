<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Estado de cuenta</title>
</head>
<style>
    @page {margin: 235px 30px 62px 30px}

    body{
        font-family: 'Lucida Console',monospace;
    }
    header{
        position: fixed;
        top:-235px;
        width: 100%;
        text-align: center;
    }

    footer{
        position: fixed;
        bottom: -62px;
    }

	.page-break {
	    page-break-after: always;
	}

</style>
<body>
    <header>
        <div style="line-height: 100%">
            <img height="75px" width="150px" src="img/logo.png" style="float: left; margin-top:10px<b>">
            <h2>MUNICIPALIDAD DE GUATEMALA <br><br> Estado de Cuenta </b></h2>
        </div>

        <table align="center" style="border-top: 1px solid black; border-bottom: 1px solid black; width: 100%; margin-bottom:10px;">
            <tr>
                <td><b>CODIGO:</b> {{ $data['H_PROPIETARIO']['BPEXT'] }}</td>
                <td><b>INTERLOCUTOR:</b> {{ $data['H_PROPIETARIO']['GPART'] }}</td>
            </tr>
            <tr>
                <td><b>PROPIETARIO:</b> {{ isset($data['H_PROPIETARIO']['ZZFULL_NAME']) ? $data['H_PROPIETARIO']['ZZFULL_NAME'] : $data['T_CABECERA']['BPEXT'] }}</td>
                <td><b>FECHA:</b> {{ date('d-m-Y') }}</td>
            </tr>
            <tr>
                <td colspan="2"><b>DOMICILIO:</b> {{ $data['H_PROPIETARIO']['ZZFULL_ADDRESS'] }}</td>
            </tr>
        </table>

        <table align="center" style="width: 100%; margin-bottom:10px;">
            <tr>
                <td><b>CODIGO:</b> {{ $data['T_CABECERA']['BPEXT'] }}</td>
                <td><b>INTERLOCUTOR:</b> {{ $data['T_CABECERA']['GPART'] }}</td>
            </tr>
            <tr>
                <td colspan="2"><b>DIRECCIÓN:</b> {{ $data['T_CABECERA']['ZZFULL_ADDRESS'] }}</td>
            </tr>
        </table>
    </header>

    <footer>
        <p><b>FECHA LIMITE DE PAGO PARA IUSI Y C.I.:</b> El siguiente mes calendario a la fecha de vencimiento detallada en este documento. <br>
        <b>OTROS PAGOS:</b>  La fecha de vencimiento detallada en cada cargo</p>
    </footer>

    <main>
        <table style="border-collapse: collapse;" align="center" >
            <thead>
                <th style="border: 1px solid black; border-collapse: collapse;">Fecha de Transac</th>
                <th style="border: 1px solid black; border-collapse: collapse;">Fecha de Vencim</th>
                <th style="border: 1px solid black; border-collapse: collapse;">Concepto</th>
                <th style="border: 1px solid black; border-collapse: collapse;">Descripción</th>
                <th style="border: 1px solid black; border-collapse: collapse;">Periodo</th>
                <th style="border: 1px solid black; border-collapse: collapse;">Recibo</th>
                <th style="border: 1px solid black; border-collapse: collapse;">Cargo</th>
                <th style="border: 1px solid black; border-collapse: collapse;">Abono</th>
                <th style="border: 1px solid black; border-collapse: collapse;">Saldo</th>
            </thead>
            <tbody style="font-size: 12px">
                @php
                    $cargo = 0;
                    $abono = 0;
                @endphp
                @foreach ($data['T_ECUENTA'] as $item)
                    @if ($item['ZZRECIBO'] && !strpos($item['ZZCREDITO'],'-'))

                        <tr>
                            <td style="border: 1px solid black; border-collapse: collapse; text-align:center">{{ date('d.m.Y',strtotime($item['BLDAT'])) }}</td>
                            <td style="border: 1px solid black; border-collapse: collapse; text-align:center">{{ date('d.m.Y',strtotime($item['FAEDN'])) }}</td>
                            <td style="border: 1px solid black; border-collapse: collapse;">{{ $item['TXT30'] }}</td>
                            <td style="border: 1px solid black; border-collapse: collapse;">{{ $item['OPTXT'] }}</td>
                            <td style="border: 1px solid black; border-collapse: collapse;">{{ $item['TXT50'] }}</td>
                            <td style="border: 1px solid black; border-collapse: collapse; text-align:center;">{{ $item['ZZRECIBO'] }}</td>
                            <td style="border: 1px solid black; border-collapse: collapse; text-align:center;">{{ strpos($item['ZZDEBITO'],'-') ? number_format('-'.str_replace("-","",$item['ZZDEBITO']),2) : number_format($item['ZZDEBITO'],2) }}</td>
                            <td style="border: 1px solid black; border-collapse: collapse; text-align:center;">{{ strpos($item['ZZCREDITO'],'-') ? number_format('-'.str_replace("-","",$item['ZZCREDITO']),2) : number_format($item['ZZCREDITO'],2) }}</td>
                            <td style="border: 1px solid black; border-collapse: collapse; text-align:center;">{{ strpos($item['ZZSALDO'],'-') ? number_format('-'.str_replace("-","",$item['ZZSALDO']),2) : number_format($item['ZZSALDO'],2) }}</td>
                        </tr>
                        @php
                            $cargo += strpos($item['ZZDEBITO'],'-') ? floatval('-'.str_replace("-","",$item['ZZDEBITO'])) : floatval($item['ZZDEBITO']);
                            $abono += strpos($item['ZZCREDITO'],'-') ? floatval('-'.str_replace("-","",$item['ZZCREDITO'])) : floatval($item['ZZCREDITO']);
                        @endphp
                    @endif
                @endforeach
            </tbody>
            <tfoot style="font-size: 14px">
                <tr>
                    <td colspan="6" align="right"><b>Subtotal: </b></td>
                    <td style="border: 1px solid black; border-collapse: collapse; text-align:center;">{{ number_format($cargo,2) }}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; text-align:center;">{{ number_format($abono,2) }}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; text-align:center;">{{ number_format($cargo - $abono,2) }}</td>
                </tr>
                <tr>
                    <td colspan="6" align="right"><b>TOTAL: </b></td>
                    <td style="border: 1px solid black; border-collapse: collapse; text-align:center;"><b>{{ number_format($cargo,2) }} </b></td>
                    <td style="border: 1px solid black; border-collapse: collapse; text-align:center;"><b>{{ number_format($abono,2) }} </b></td>
                    <td style="border: 1px solid black; border-collapse: collapse; text-align:center;"><b>{{ number_format($cargo - $abono,2) }} </b></td>
                </tr>
            </tfoot>
        </table>
    </main>

    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(745, 573, "Pág $PAGE_NUM / $PAGE_COUNT", $font, 10);
            ');
        }
    </script>

</body>
</html>
