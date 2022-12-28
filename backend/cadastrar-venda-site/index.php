<?php
date_default_timezone_set('America/Sao_Paulo');
include '../conexao.php';
$idCliente  = (empty($_GET['id'])) ? '' : $_GET['id'];

if ((is_numeric($idCliente) && $idCliente > 0 )|| strtoupper($idCliente) == 'TODOS')
{
    if(strtoupper($idCliente) == 'TODOS'){
        $buscaClientes = "SELECT idCliente FROM `parametros` where status = 1 and tipoFatura = 'M'";
    }
    else{
        $buscaClientes = "SELECT idCliente FROM `parametros` where idCliente = $idCliente and status = 1";
    }

    $resultBuscaCliente = mysqli_query($conn,$buscaClientes);

    foreach($resultBuscaCliente as $rBuscaCliente) {
        $resultBusca = json_decode(json_encode($rBuscaCliente));
        $idCliente = $resultBusca->idCliente;

        $query = "SELECT v.clientes_id
        , c.nomeCliente
        , sum(p.precoVenda) valor
        , if (CHAR_LENGTH(vencimento) < 2, concat(substring(now(), 1, 8), 0, c.vencimento), concat(substring(now(), 1, 8), c.vencimento)) vencimento
        , (select auto_increment from information_schema.tables where table_name = 'vendas') idVenda
        , CONCAT( 'Lista IPTV - Ref: ', (select concat(substring(now(), 6, 2), '/', substring(now(), 1, 4))), ' Nº: ', (select auto_increment from information_schema.tables where table_name = 'vendas') ) AS descricao
        , (select auto_increment from information_schema.tables where table_name = 'lancamentos') idLancamento
        , par.produtosFatura
        , par.fator
        , CASE
        when par.tipoFatura = 'M'
        then 'S'
        when par.tipoFatura = 'B'
        then 'S'
        else 'N'
        END AS geraFatura
        from vendas as v
        inner join itens_de_vendas as idv
        on v.idVendas = idv.vendas_id
        inner join produtos as p
        on idv.produtos_id = p.idProdutos
        inner join parametros as par
        on v.clientes_id = par.idCliente
        inner join clientes as c
        on v.clientes_id = c.idClientes
        where v.clientes_id = $idCliente
        and par.status = 1
        and v.idVendas in (
                                SELECT max(v2.idVendas) venda
                                from vendas as v2
                                inner join itens_de_vendas as idv
                                on v2.idVendas = idv.vendas_id
                                inner join produtos as p
                                on idv.produtos_id = p.idProdutos
                                where v2.clientes_id = v.clientes_id
                            )
        order by v.idVendas desc";

        $result = mysqli_query($conn,$query);
            foreach($result as $r) {

                $obj= json_decode(json_encode($r));
                                
                if($obj->geraFatura == 'S'){

                    $obj->valor = number_format(($obj->valor * $obj->fator),2,'.',',');

                    $gerarLancamento = "INSERT INTO `lancamentos` (`descricao`, `valor`, `data_vencimento`, `baixado`, `cliente_fornecedor`, `forma_pgto`, `tipo`, `clientes_id`, `vendas_id`, `usuarios_id`) 
                    VALUES ('$obj->descricao', '$obj->valor', '$obj->vencimento',  '0', '$obj->nomeCliente', 'Pix', 'receita', '$obj->clientes_id', '$obj->idVenda', '1')";

                    mysqli_query($conn,$gerarLancamento);
                //     echo $gerarLancamento;
                //     echo "</br>";

                   $gerarVenda = "INSERT INTO `vendas` (`dataVenda`, `valorTotal`,  `faturado`, `clientes_id`, `usuarios_id`, `lancamentos_id`, `lancamentos_descricao`, `pago`) 
                   VALUES ('$obj->vencimento', '$obj->valor', '1', '$obj->clientes_id', '1', '$obj->idLancamento', '$obj->descricao', '0')";
                    
                   mysqli_query($conn,$gerarVenda);
                    // echo $gerarVenda;
                    // echo "</br>";

                    $buscaProdutos = "SELECT idProdutos, precoVenda FROM `produtos` where idProdutos in ($obj->produtosFatura)";
                    $resultbuscaProdutos = mysqli_query($conn,$buscaProdutos);

                    foreach($resultbuscaProdutos as $rBuscaProdutos) {

                        $resBuscaProdutos = json_decode(json_encode($rBuscaProdutos));

                        $valorUnitario = $resBuscaProdutos->precoVenda;

                        $resBuscaProdutos->precoVenda = number_format(($resBuscaProdutos->precoVenda * $obj->fator),2,'.',',');
                        
                        $gerarVendaItens = "INSERT INTO `itens_de_vendas` (`subTotal`, `quantidade`, `preco`, `vendas_id`, `produtos_id`) 
                        VALUES ('$resBuscaProdutos->precoVenda', '$obj->fator', '$valorUnitario', '$obj->idVenda', '$resBuscaProdutos->idProdutos')";
                        mysqli_query($conn,$gerarVendaItens);
                        // echo $gerarVendaItens;
                        // echo "</br>";
                    }
              }        
            }
    }
    mysqli_close($conn);
}
?>