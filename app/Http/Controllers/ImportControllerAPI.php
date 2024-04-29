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
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjU5Y2FiZDIyNDlmMWZkZWY0NDlkMDUyMzg3YzhmZmE2ODViYTNjZmM4NGQ3ODc0YmQwZjUyYjE0ZTk2ZDIxOTc5MzQ3ZWU2YjkyZDBjNmEzIn0.eyJhdWQiOiIxMTg5IiwianRpIjoiNTljYWJkMjI0OWYxZmRlZjQ0OWQwNTIzODdjOGZmYTY4NWJhM2NmYzg0ZDc4NzRiZDBmNTJiMTRlOTZkMjE5NzkzNDdlZTZiOTJkMGM2YTMiLCJpYXQiOjE3MTQzOTc4OTgsIm5iZiI6MTcxNDM5Nzg5OCwiZXhwIjoxNzE2OTg5ODk3LCJzdWIiOiI2NjQxIiwic2NvcGVzIjpbXX0.MeSHpJxQAAfB8EhqoNA7PxTOGlRtIjbKt7-_ayqQy9Zeep-xUvPIZUcKZHHMQ0SqOVHiVMWUTTWakIP5vPpHCOZhm-WAGkYXoBV-vG0njIKz4SCyczzuMpkMY8chyqO07uQctOmjDHOZEcbWfmPcT1Wey46uAQjKpD0TCrrfjsC7oi7jkNfjn6OGauKYwf2Kak5MnpL94Ask-T-cSYqp4pt92WFCO5vKnNzYOkPqiAvFouPrBjwVGoiy07KTasaHROGm0IrcW-HOaUyGdclZektg2ESO0lb0alx6qqw84qSXfFNg09WV4KJLx0Bx_qi87NwDbyXmiuQ2QQU0xLZy-uDX4-jbv00Ikx4vkbCeij1GUoSbCRsuEM4KyZloahot6Id6-iNCENcVsQcH-n9_NshivmDm09Fv1kcxaqi6o27oqyAC6YSPLinX_lz2DnabL0mouy31vYbgX1qxEIP0j5MDlGtLBOEumUsTvKISrMdE4pq534llc29w57deCwwG3zMwF6crlXez1UhOJTlREOk-xrCpEjWyBMJkKInTJnvz2TFALqXloXYAbgSagYbTRLrJXh1GvOJsWppL3clhhUYdjl8A0FTyuo4j9vHjvJLcUqtfjSfo6w5QcABKFxx0N8ePADCV2z7UrWtbwbS3xlnXhmGYKCpt_MC5qsCDCxM',
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
