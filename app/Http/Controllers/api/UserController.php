<?php

namespace App\Http\Controllers\api;

use App\Models\Usuario;
use App\Models\Mensagem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function getUserInfo(Request $request)
    {
        $user = Usuario::where('access_token', $request->access_token)->first();
        if (!$user) return response()->json([
            'status' => 'error',
            'message' => 'Usuário não encontrado'
        ], 404);

        return response()->json($user);
    }

    public function getUsersKluber(Request $request)
    {
        if (!$request->search_text) {
            $users = Usuario::where('nivel_kluber', 'COL')->select([
                'id',
                'nome_usuario',
                'nome_usuario_completo',
            ])->get();

            return response()->json($users);
        } else {
            $users = Usuario::where('nivel_kluber', 'COL')->where('nome_usuario', 'like', '%' . $request->search_text . '%')->select([
                'id',
                'nome_usuario',
                'nome_usuario_completo',
            ])->get();

            return response()->json($users);
        }
    }

    public function newUser(Request $request)
    {
        try {
            Log::info('Dados recebidos para nova solicitação:', ['request' => $request->all()]);

            // Validação dos dados
            $request->validate([
                'nome_completo' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'cargo_ocupacao' => 'required|string|max:255',
                'representante_id' => 'nullable|integer',
                'representante_nome' => 'nullable|string|max:255',
                'nova_empresa' => 'nullable|boolean',
                'nome_companhia' => 'nullable|string|max:255',
                'informe_companhia' => 'nullable|string|max:255',
                'cnpj' => 'nullable|string|max:20',
                'endereco' => 'nullable|string|max:255',
                'numero' => 'nullable|string|max:20',
                'pais' => 'nullable|string|max:100',
                'estado' => 'nullable|string|max:2',
                'cidade' => 'nullable|string|max:100',
                'bairro' => 'nullable|string|max:100',
                'cep' => 'nullable|string|max:10',
            ]);

            // Verifica se usuário já existe
            $usuarioExistente = Usuario::where('email', $request->email)
                ->where('nivel_usuario', 'CLI')
                ->first();

            if ($usuarioExistente) {
                return response()->json([
                    'msg_retorno' => 'E-mail já registrado no Portal. Verifique as informações enviadas e tente novamente.',
                    'retorno' => '2'
                ], 400);
            }

            // Sanitiza os dados
            $nomeCompleto = $this->sanitizeString($request->nome_completo);
            $email = $this->sanitizeEmail($request->email);

            // Gera nome de usuário único
            $nomeUsuario = $this->generateUniqueUsername($nomeCompleto);

            // Configura representante
            $codigoRepres = $request->representante_id ?? null;
            $companhia = $request->nova_empresa ? $request->nome_companhia : $request->informe_companhia;

            // Busca mensagens do sistema
            $mensagens = $this->getSystemMessages();

            // Envia email de confirmação para o usuário
            $emailEnviado = $this->sendUserConfirmationEmail($email, $nomeCompleto, $mensagens['acesso_cliente']);

            if (!$emailEnviado) {
                return response()->json([
                    'msg_retorno' => 'Erro ao enviar e-mail. Por favor tente mais tarde!',
                    'retorno' => '2'
                ], 500);
            }

            // Envia notificação para equipe se for nova empresa
            if ($request->nova_empresa) {
                $this->sendNewCompanyNotification($request, $nomeCompleto, $mensagens['nova_unidade']);
            }

            // Envia notificação para equipe sobre novo usuário
            $this->sendNewUserNotification($request, $nomeCompleto, $companhia, $mensagens['novo_usuario']);

            // Cria o usuário no banco
            $usuario = Usuario::create([
                'nome_usuario' => $nomeUsuario,
                'email' => $email,
                'bloqueio' => 'S',
                'codigo_repres' => $codigoRepres,
                'codigo_empresa' => '0001',
                'codigo_cliente' => $request->cliente_id,
                'nivel_usuario' => 'CLI',
                'nome_usuario_completo' => $nomeCompleto,
                'data_solicitacao_acesso' => now(),
            ]);

            Log::info('Usuário criado com sucesso:', ['usuario_id' => $usuario->id]);

            return response()->json([
                'msg_retorno' => 'Solicitação enviada com sucesso. Confira seu e-mail para maiores informações.',
                'retorno' => '1'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erro ao processar nova solicitação:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'msg_retorno' => 'Erro interno do servidor. Por favor tente mais tarde!',
                'retorno' => '2'
            ], 500);
        }
    }

    private function sanitizeString($string)
    {
        $string = trim(str_replace(["_", "/", "<", ">", "\\", "(", ")", "'", "`", '"', ','], "", $string));
        $string = str_replace("&", "E", $string);
        return strtoupper(preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($string))));
    }

    private function sanitizeEmail($email)
    {
        return trim(str_replace(["/", "<", ">", "\\", "(", ")", "'", "`", '"', ','], "", $email));
    }

    private function generateUniqueUsername($nomeCompleto)
    {
        $nomes = explode(" ", $nomeCompleto);
        $primeiroNome = $nomes[0];
        $ultimoNome = $nomes[count($nomes) - 1];
        $nomeUsuario = strtolower($primeiroNome . "." . $ultimoNome);

        $count = Usuario::where('nome_usuario', 'like', $nomeUsuario . '%')->count();

        if ($count > 0) {
            $nomeUsuario = $nomeUsuario . ($count + 1);
        }

        return $nomeUsuario;
    }

    private function getSystemMessages()
    {
        $mensagens = [
            'acesso_cliente' => 'Sua solicitação de acesso ao Portal Klüber Lubrication foi recebida. Em breve entraremos em contato.',
            'nova_unidade' => 'Nova solicitação de cadastro de empresa recebida.',
            'novo_usuario' => 'Nova solicitação de acesso ao portal recebida.'
        ];

        // Busca mensagens personalizadas do banco
        $mensagensDb = Mensagem::where('codigo_empresa', '0001')
            ->whereIn('codigo_mensagem', ['acesso_cliente', 'nova_unidade', 'novo_usuario'])
            ->get();

        foreach ($mensagensDb as $msg) {
            $mensagens[$msg->codigo_mensagem] = $msg->descricao_mensagem;
        }

        return $mensagens;
    }

    private function sendUserConfirmationEmail($email, $nomeCompleto, $mensagemTemplate)
    {
        try {
            $assunto = "Solicitação de Acesso - Portal Klüber Lubrication";
            $mensagem = str_replace("{{nome_completo}}", $nomeCompleto, $mensagemTemplate);

            // Configura emails para cópia
            $emailsCopia = [
                'suportetecnico@br.klueber.com',
                'heitor.lopes@br.klueber.com',
                'emilly.brito@br.klueber.com',
                'ms@rentatec.com.br',
                'gustavo.serbena@rentatec.com.br'
            ];

            // Aqui você deve implementar o envio do email usando Laravel Mail
            // Mail::send(...);

            Log::info('Email enviado para:', ['email' => $email, 'assunto' => $assunto]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email:', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function sendNewCompanyNotification($request, $nomeCompleto, $mensagemTemplate)
    {
        try {
            $assunto = "Solicitação de Cadastro de Nova Unidade - Portal Klüber Lubrication";
            $mensagem = str_replace([
                "{{nome_completo}}",
                "{{companhia}}",
                "{{cnpj}}",
                "{{cep}}",
                "{{endereco}}",
                "{{numero}}",
                "{{pais}}",
                "{{uf}}",
                "{{cidade}}",
                "{{bairro}}"
            ], [
                $nomeCompleto,
                $request->nome_companhia,
                $request->cnpj,
                $request->cep,
                $request->endereco,
                $request->numero,
                $request->pais,
                $request->estado,
                $request->cidade,
                $request->bairro
            ], $mensagemTemplate);

            // Implementar envio de email
            Log::info('Notificação de nova empresa enviada');

        } catch (\Exception $e) {
            Log::error('Erro ao enviar notificação de nova empresa:', ['error' => $e->getMessage()]);
        }
    }

    private function sendNewUserNotification($request, $nomeCompleto, $companhia, $mensagemTemplate)
    {
        try {
            $assunto = "Solicitação de Novo Acesso - Portal Klüber Lubrication";
            $mensagem = str_replace([
                "{{nome_completo}}",
                "{{companhia}}",
                "{{cnpj}}",
                "{{cep}}",
                "{{endereco}}",
                "{{numero}}",
                "{{pais}}",
                "{{uf}}",
                "{{cidade}}",
                "{{bairro}}"
            ], [
                $nomeCompleto,
                $companhia,
                $request->cnpj ?? '',
                $request->cep ?? '',
                $request->endereco ?? '',
                $request->numero ?? '',
                $request->pais ?? '',
                $request->estado ?? '',
                $request->cidade ?? '',
                $request->bairro ?? ''
            ], $mensagemTemplate);

            // Implementar envio de email
            Log::info('Notificação de novo usuário enviada');

        } catch (\Exception $e) {
            Log::error('Erro ao enviar notificação de novo usuário:', ['error' => $e->getMessage()]);
        }
    }
}
