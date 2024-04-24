<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Cliente;


class ImportControllerAPI extends Controller
{
    public function importData()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ',
            'Content-Type' => 'application/json',
        ])->get('https://api.demo.hubsoft.com.br/api/v1/integracao/cliente', [
            'busca' => 'codigo_cliente',
            'termo_busca' => '2361',
        ]);


        $data = $response->json();

        foreach ($data['clientes'] as $clienteData) {
            $cliente = Cliente::where('codigo_cliente', $clienteData['codigo_cliente'])->first();

            if (!$cliente) {
                $cliente = new Cliente();
            }

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

            if (isset($clienteData['data_nascimento'])) {
                $cliente->data_nascimento = $clienteData['data_nascimento'];
            }

            $cliente->save();
        }

        return view('api.result')->with('data', $data);
    }
}