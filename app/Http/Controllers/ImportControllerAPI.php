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
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjliN2VkYzJiOWNmNTg3MWU1NjkwOTNmM2RkMGYwYzg3NjUwYzU1ODgxNDZlZWJhYWUxNWM1ODhlN2FjODI1NzdhNTYzOTAxMmEyOTA5OTk1In0.eyJhdWQiOiIxMTg5IiwianRpIjoiOWI3ZWRjMmI5Y2Y1ODcxZTU2OTA5M2YzZGQwZjBjODc2NTBjNTU4ODE0NmVlYmFhZTE1YzU4OGU3YWM4MjU3N2E1NjM5MDEyYTI5MDk5OTUiLCJpYXQiOjE3MTM5ODE5MzIsIm5iZiI6MTcxMzk4MTkzMiwiZXhwIjoxNzE2NTczOTMyLCJzdWIiOiI2NjQxIiwic2NvcGVzIjpbXX0.fDknDnTX9i_xepfrdY9kdLX8fnAzTJ0lHew8zuu3JylUYpF4fP1YgnM6qBS1HliHrgw2QpVgsFPF2XjDder2ZN3G3uOVjW54IHbW24O8mczap6vRTecSElMPMPSK_CB1j-LVDzKKDj0sQ9FfLDRqU3IpcBwNmRu-PPmPNPs_clHv9TyS40YFesR7UeWpMTxBFio3b6cQawoEilTbwpvIrpym-cv_A2RNSy_7frUU48pDkq8TqQQomSDf-UNRWuxqndJnvvRJUV5p1-4MH0ISNTQ55z-VU0UhsNdt8RRuoBnqDOJq0K8_Q302GnqE0YKAkmPo5ZMTV56kzn-wIKsW-aOFJgFlQDw9dTWdE4KVetZpeh8xYri0tBEWOR0ZXb_qpHrWbPTREuR6OuDStyZhhzQl9bGme8w5E12IjE8gfgJcy7uZq7ah0b5GzB50GMnbE3n5jkRzQ5GxQmPqdp28-xUTBlFDqGSbtN6tqz5fFfG_6kIK44vD98EDvqP6ElLAZ-Zp3bWPnDPH7x8Jebh0r7-d4zdaDTeyR5ZUwESzV4O9GxrexRNKmM8INlNXCpSuptGYuWjEiN8DxOx04yTkE7KleQuCcaoi9p-nHG0sDSzBhRjHz9QVkDicsEp9EnG9zyA4RxUzFwpNF30fJ55VOdHGrJZ3yoOxK7is0RDs4KM',
            'Content-Type' => 'application/json',
        ])->get('https://api.demo.hubsoft.com.br/api/v1/integracao/cliente', [
            'busca' => 'codigo_cliente',
            'termo_busca' => '2362',
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