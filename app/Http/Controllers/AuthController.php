<?php

namespace App\Http\Controllers;

use Str;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    public function login(Request $request)
    {


        if ($request->getMethod() == 'GET') return view('auth.login', get_defined_vars());

        $verificaSenha = $this->verificaSenha($request);
        if ($verificaSenha == false) {
            return redirect()->back()->with('error', 'Usuario ou Senha invalidos!');
        }
        if (Auth::validate(['nome_usuario' => $request->usuario, 'password' => $request->password])) {
            if (Auth::attempt(['nome_usuario' => $request->usuario, 'password' => $request->password])) {
                return redirect()->route('dashboard');
            }
        } else {
            return redirect()->back()->with('error', 'Usuario ou Senha invalidos!');
        }
    }

    public function verificaSenha(Request $request)
    {
        $senha = false;
        $usuario = Usuario::where('nome_usuario', $request->usuario)->first();


        if (empty($usuario->password)) {
            function resto($dividendo, $divisor)
            {
                return ($dividendo - floor(($dividendo / $divisor)) * $divisor);
            }
            function criptografa_RSA($mensagem)
            {
                $e = 7;
                $N = 19009 * 60007;

                $tam = strlen($mensagem);

                $vetMSG = array($tam);
                $crpMSG = array($tam);

                for ($i = 0; $tam > $i; $i++) {/* criando o pre-codigo para a criptografia */
                    $vetMSG[$i] = ord($mensagem[$i]);
                }
                for ($i = 0; $tam > $i; $i++) {
                    $potencia = pow($vetMSG[$i], $e);
                    $crpMSG[$i] = resto($potencia, $N);/* criptografando o pre-codigo	*/
                }
                return (implode("", $crpMSG));/* juntando os numeros */
            }
            function criptografiaPadrao($palavra, $palavra_complementar = false)
            {
                if ($palavra_complementar)
                    return (md5("$palavra$palavra_complementar"));
                else
                    return (md5($palavra));
            }

            function criptografiaSenha($senha)
            {

                return criptografiaPadrao(criptografa_RSA($senha));
            }
            if (criptografiaSenha($request->password) == $usuario->senha) {
                $usuario->update(['password' => Hash::make($request->password)]);
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
    public function loginApi(Request $request)
    {
        $verificaSenha = $this->verificaSenha($request);

        if ($verificaSenha == false) {
            return response()->json(['login_error' => 'Verifique usuario ou senha!'], 422);
        }

        $update_token['access_token'] = (string) \Str::uuid();
        $update_token['token_expires_in'] = date('Y-m-d H:i:s', strtotime('+24 Hours'));

        $remember = $request->remember ? true : false;

        $authValid = Auth::guard('web')->validate(['nome_usuario' => $request->usuario, 'password' => $request->password]);

        if ($authValid) {

            $token = Crypt::encryptString(collect(($update_token['access_token'])));
            Usuario::where('nome_usuario', $request->usuario)->update(['access_token' => $token]);

            return response()->json(['access_token' => $token, 'token_expires_in' => $update_token['token_expires_in']]);
        } else {
            return response()->json('Email ou Senha incorretos!', 422);
        }
    }

    public function recuperaSenha(Request $request)
    {
        $usuario = Usuario::where('nome_usuario', $request->usuario)->first();

        if (!$usuario) {
            return response()->json(['error' => 'Usuario não encontrado!'], 422);
        }

        // Gere um token único.
        $token = \Str::random(60);

        // Armazene o token no banco de dados.
        DB::table('password_reset_tokens')->insert([
            'email' => $usuario->email,
            'user_id' => $usuario->id,
            'usuario' => $usuario->nome_usuario,
            'token' => $token,
            'created_at' => now(),
        ]);

        // Envie o token para o e-mail do usuário. (Estou assumindo que seu modelo de usuário tem um campo de e-mail.)
        // Você precisará configurar e-mail em seu projeto Laravel e talvez criar uma visão para o e-mail.
        // Mail::to($usuario->email)->send(new PasswordResetMail($token, $usuario->nome_usuario));

        return response()->json(['message' => 'E-mail enviado com sucesso!'], 200);
    }
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }
    public function handleReset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6',
        ]);

        $passwordReset = DB::table('password_reset_tokens')->where('token', $request->token)->first();

        if (!$passwordReset) {
            return response()->json(['message' => 'Token inválido!'], 422);
        }

        $user = Usuario::find($passwordReset->user_id);

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Email não encontrado!']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Opcionalmente, exclua o token de redefinição após ser usado
        DB::table('password_reset_tokens')->where('token', $request->token)->delete();

        return response()->json(['message' => 'Senha alterada com sucesso!'], 200);
    }

    public function logout()
    {
        Auth::logout();
        // setCookie('remember_token_app');
        return response()->json(['url' => route('login')]);
    }
}
