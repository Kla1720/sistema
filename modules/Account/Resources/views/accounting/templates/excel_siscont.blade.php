<table>
    <tr>
        <!--1--><td>Vou.Origen</td>
        <!--2--><td>Vou.Numero</td>
        <!--3--><td>Vou.Fecha</td>
        <!--4--><td>Doc</td>
        <!--5--><td>Numero</td>
        <!--6--><td>Fec.Doc</td>
        <!--7--><td>Fec.Venc.</td>
        <!--8--><td>Codigo</td>
        <!--9--><td>Valor Exp.</td>
        <!--10--><td>B.Imponible</td>
        <!--11--><td>Inafecto</td>
        <!--12--><td>Exonerado</td>
        <!--13--><td>I.S.C.</td>
        <!--14--><td>IGV</td>
        <!--15--><td>OTROS TRIB.</td>
        <!--16--><td>IMP. BOLSA</td>
        <!--17--><td>Moneda</td>
        <!--18--><td>TC</td>
        <!--19--><td>Glosa</td>
        <!--20--><td>Cta Ingreso</td>
        <!--21--><td>Cta IGV</td>
        <!--22--><td>Cta O. Trib.</td>
        <!--23--><td>Cta x Cobrar</td>
        <!--24--><td>C.Costo</td>
        <!--25--><td>Presupuesto</td>
        <!--26--><td>R.Doc</td>
        <!--27--><td>R.numero</td>
        <!--28--><td>R.Fecha</td>
        <!--29--><td>RUC</td>
        <!--30--><td>R.Social</td>
        <!--31--><td>Tipo</td>
        <!--32--><td>Tip.Doc.Iden</td>
        <!--33--><td>Medio de Pago</td>
        <!--34--><td>Apellido 1</td>
        <!--35--><td>Apellido 2</td>
        <!--36--><td>Nombre</td>
        <!--37--><td>P.origen</td>
        <!--37--><td>P.vou</td>
        <!--39--><td>P.fecha</td>
        <!--40--><td>P.fecha D.</td>
        <!--41--><td>P.fecha V.</td>
        <!--41--><td>P.cta cob</td>
        <!--43--> <td>P.m.pago</td>
        <!--44--><td>P.doc</td>
        <!--45--><td>P.num doc</td>
        <!--46--><td>P.moneda</td>
        <!--47--><td>P.tc</td>
        <!--48--><td>P.monto</td>
        <!--49--><td>P.glosa	P.fe</td>
        <!--50--><td>Retencion 0/1</td>
        <!--51--><td>PDB ndes</td>
        <!--52--><td>CodTasa</td>
        <!--53--><td>Ind.Ret</td>
        <!--54--><td>B.Imp</td>
        <!--55--><td>IGV</td>
    </tr>

    @foreach($records as $row)
        @if($row['state_type_id'] == '11')
            <tr>
                <!--1--><td>02</td>
                <!--2--><td>{{ $loop->index+1 }}</td>
                <!--3--><td>{{ $row['date_of_issue'] }}</td>
                <!--4--><td>{{ $row['document_type_id'] }}</td>
                <!--5--><td>{{ $row['series'] }}-{{ $row['number'] }}</td>
                <!--6--><td>{{ $row['date_of_issue'] }}</td>
                <!--7--><td>{{ $row['date_of_due'] }}</td>
                <!--8--><td>00000001</td>
                <!--9--><td>{{ $row['total_exportation'] }}</td>
                <!--10--><td>{{ $row['total_taxed'] }}</td>
                <!--11--><td>{{ $row['total_unaffected'] }}</td>
                <!--12--><td>0</td>
                <!--13--><td>{{ $row['total_isc'] }}</td>
                <!--14--><td>{{ $row['total_igv'] }}</td>
                <!--15--><td></td>
                <!--16--><td>{{ $row['total_plastic_bag_taxes'] }}</td>
                <!--17--><td>{{ $row['currency'] }}</td>
                <!--18--><td>{{ $row['exchange_rate_sale'] }}</td>
                <!--19--><td>VENTA</td>
                <!--20--><td>{{ $row['cta_ingreso'] }}</td>
                <!--21--><td>{{ $row['cta_igv'] }}</td>
                <!--22--><td></td>
                <!--23--><td>{{ $row['cta_x_cobrar'] }}</td>
                <!--24--><td></td>
                <!--25--><td></td>
                <!--26--><td></td>
                <!--27--><td></td>
                <!--28--><td></td>
                <!--29--><td>00000001</td>
                <!--30--><td>ANULADO</td>
                <!--31--><td>2</td>
                <!--32--><td>0</td>
                <!--33--><td></td>
                <!--34--><td></td>
                <!--35--><td></td>
                <!--36--><td></td>
                <!--37--><td></td>
                <!--38--><td></td>
                <!--39--><td></td>
                <!--40--><td></td>
                <!--41--><td></td>
                <!--42--><td></td>
                <!--43--><td></td>
                <!--44--><td></td>
                <!--45--><td></td>
                <!--46--><td></td>
                <!--47--><td></td>
                <!--48--><td>0</td>
                <!--49--><td></td>
                <!--50--><td></td>
                <!--51--><td></td>
                <!--52--><td></td>
                <!--53--><td></td>
                <!--54--><td></td>
                <!--55--><td></td>
            </tr>
        @else
            <tr>
                <!--1--><td>02</td>
                <!--2--><td>{{ $loop->index+1 }}</td>
                <!--3--><td>{{ $row['date_of_issue'] }}</td>
                <!--4--><td>{{ $row['document_type_id'] }}</td>
                <!--5--><td>{{ $row['series'] }}-{{ $row['number'] }}</td>
                <!--6--><td>{{ $row['date_of_issue'] }}</td>
                <!--7--><td>{{ $row['date_of_due'] }}</td>
                <!--8--><td>{{ $row['customer_number'] }}</td>
                <!--9--><td>{{ $row['total_exportation'] }}</td>
                <!--10--><td>{{ $row['total_taxed'] }}</td>
                <!--11--><td>{{ $row['total_unaffected'] }}</td>
                <!--12--><td>{{ $row['total_exonerated'] }}</td>
                <!--13--><td>{{ $row['total_isc'] }}</td>
                <!--14--><td>{{ $row['total_igv'] }}</td>
                <!--15--><td></td>
                <!--16--><td>{{ $row['total_plastic_bag_taxes'] }}</td>
                <!--17--><td>{{ $row['currency'] }}</td>
                <!--18--><td>{{ $row['exchange_rate_sale'] }}</td>
                <!--19--><td>VENTA</td>
                <!--20--><td>{{ $row['cta_ingreso'] }}</td>
                <!--21--><td>{{ $row['cta_igv'] }}</td>
                <!--22--><td></td>
                <!--23--><td>{{ $row['cta_x_cobrar'] }}</td>
                <!--24--><td></td>
                <!--25--><td></td>
                <!--26--><td></td>
                <!--27--><td></td>
                <!--28--><td></td>
                <!--29--><td>{{ $row['customer_number'] }}</td>
                <!--30--><td>{{ $row['customer_name'] }}</td>
                <!--31--><td>2</td>
                <!--32--><td>{{ $row['customer_identity_document_type_id'] }}</td>
                <!--33--><td></td>
                <!--34--><td></td>
                <!--35--><td></td>
                <!--36--><td></td>
                <!--37--><td></td>
                <!--38--><td></td>
                <!--39--><td></td>
                <!--40--><td></td>
                <!--41--><td></td>
                <!--42--><td></td>
                <!--43--><td></td>
                <!--44--><td></td>
                <!--45--><td></td>
                <!--46--><td></td>
                <!--47--><td></td>
                <!--48--><td></td>
                <!--49--><td></td>
                <!--50--><td></td>
                <!--51--><td></td>
                <!--52--><td></td>
                <!--53--><td></td>
                <!--54--><td></td>
                <!--55--><td></td>
            </tr>
        @endif
    @endforeach
</table>
