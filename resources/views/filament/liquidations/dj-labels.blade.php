@php
    /** @var array $items  -> [['name'=>..., 'total'=>..., 'sales_count'=>int, 'pairs_count'=>int], ...] */
@endphp

@if (empty($items))
    <div style="font-size: 14px; color: #6b7280;">No hay pagos pendientes.</div>
@else
    <div style="display:flex; justify-content:center;">
        <div
            style="
            border:1px solid #e5e7eb;
            border-radius:12px;
            overflow:hidden;
            width:100%;
            max-width:660px;
            box-sizing:border-box;
        ">
            <table style="width:100%; border-collapse:collapse; font-size:13px; table-layout:fixed;">
                <colgroup>
                    <col style="width:38%">
                    <col style="width:22%">
                    <col style="width:40%">
                </colgroup>

                <thead style="background:#f9fafb;">
                    <tr>
                        <th
                            style="
                            padding:8px 12px;
                            text-align:left;
                            font-weight:600;
                            color:#374151;
                            border-bottom:1px solid #e5e7eb;
                            white-space:nowrap;
                        ">
                            DJ</th>

                        <th
                            style="
                            padding:8px 12px;
                            text-align:right;
                            font-weight:600;
                            color:#374151;
                            border-bottom:1px solid #e5e7eb;
                            white-space:nowrap;
                        ">
                            Monto a pagar</th>

                        <th
                            style="
                            padding:8px 12px;
                            text-align:left;
                            font-weight:600;
                            color:#374151;
                            border-bottom:1px solid #e5e7eb;
                            white-space:nowrap;
                        ">
                            Detalle</th>
                    </tr>
                </thead>

                <tbody style="background:#ffffff;">
                    @foreach ($items as $it)
                        <tr>
                            <td
                                style="
                                padding:7px 12px;
                                text-align:left;
                                color:#111827;
                                border-bottom:1px solid #f3f4f6;
                                line-height:1.2;
                                word-break:break-word;
                            ">
                                {{ $it['name'] }}
                            </td>

                            <td
                                style="
                                padding:7px 12px;
                                text-align:right;
                                font-weight:700;
                                color:#111827;
                                border-bottom:1px solid #f3f4f6;
                                white-space:nowrap;
                            ">
                                ${{ number_format($it['total'], 2) }}
                            </td>

                            <td
                                style="
                                padding:7px 12px;
                                text-align:left;
                                color:#111827;
                                border-bottom:1px solid #f3f4f6;
                                line-height:1.2;
                            ">
                                {{ (int) ($it['sales_count'] ?? 0) }} ventas ·
                                {{ (int) ($it['pairs_count'] ?? 0) }} descargas (subs)
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot style="background:#f9fafb;">
                    <tr>
                        <td
                            style="padding:8px 12px; text-align:right; font-weight:600; color:#374151; white-space:nowrap;">
                            Total
                        </td>
                        <td
                            style="padding:8px 12px; text-align:right; font-weight:800; color:#111827; white-space:nowrap;">
                            ${{ number_format(collect($items)->sum('total'), 2) }}
                        </td>
                        <td style="padding:8px 12px; text-align:left; font-weight:600; color:#374151;">—</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif
