<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Cliente;


class ImportControllerAPI extends Controller
{
    // Função para importar dados da API
    public function importData()
    {
        // Faz uma requisição GET para a API com cabeçalhos de autorização e tipo de conteúdo
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ',
            'Content-Type' => 'application/json',
        ])->get('https://api.demo.hubsoft.com.br/api/v1/integracao/cliente', [
            'busca' => 'codigo_cliente',
            'termo_busca' => '2362',
        ]);

        // Converte a resposta para JSON
        $data = $response->json();

        // Itera sobre os clientes retornados pela API
        foreach ($data['clientes'] as $clienteData) {
            // Busca ou cria um cliente com base no código do cliente
            $cliente = Cliente::where('codigo_cliente', $clienteData['codigo_cliente'])->first();

            if (!$cliente) {
                $cliente = new Cliente();
            }

            // Preenche os dados do cliente com os dados da API
            $cliente->id_cliente = $clienteData['id_cliente'];
            $cliente->codigo_cliente = $clienteData['codigo_cliente'];
            $cliente->nome_razaosocial = $clienteData['nome_razaosocial'];
            $cliente->nome_fantasia = $clienteData['nome_fantasia'];
            $cliente->tipo_pessoa = $clienteData['tipo_pessoa'];
            $cliente->cpf_cnpj = $clienteData['cpf_cnpj'];
            $cliente->telefone_primario = $clienteData['telefone_primario'];
            $cliente->telefone_secundario = $clienteData['telefone_secundario'];
            $cliente->telefone_terciario = $clienteData['telefone_terciario'];
            $cliente->email_principal = $clienteData['email_principal'];
            $cliente->email_secundario = $clienteData['email_secundario'];
            $cliente->rg = $clienteData['rg'];
            $cliente->rg_emissao = $clienteData['rg_emissao'];
            $cliente->inscricao_municipal = $clienteData['inscricao_municipal'];
            $cliente->inscricao_estadual = $clienteData['inscricao_estadual'];
            $cliente->data_cadastro = $clienteData['data_cadastro'];
            $cliente->alerta = $clienteData['alerta'];

            // Verifica se há uma data de nascimento e a preenche se existir
            if (isset($clienteData['data_nascimento'])) {
                $cliente->data_nascimento = $clienteData['data_nascimento'];
            }

            // Salva o cliente no banco de dados
            $cliente->save();
        }

        // Retorna a view 'api.result' com os dados da API
        return view('api.result')->with('data', $data);
    }
}
