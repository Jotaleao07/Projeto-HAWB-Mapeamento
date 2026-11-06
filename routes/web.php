<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// ---------- Autenticação (único acesso público) ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/cadastro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/cadastro', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ---------- Resto do site: exigir login/cadastro ----------
Route::middleware('auth')->group(function () {

    // Páginas institucionais (estrutura original)
    Route::get('/', [PageController::class, 'inicio'])->name('inicio');
    Route::get('/inicio', [PageController::class, 'inicio'])->name('pages.inicio');
    Route::get('/sintomas', [PageController::class, 'sintomas'])->name('sintomas');
    Route::get('/locais', [PageController::class, 'locais'])->name('locais');
    Route::get('/tipos', [PageController::class, 'tipos'])->name('tipos');
    Route::get('/equipe', [PageController::class, 'equipe'])->name('equipe');

    // Formulário de contato (PHPMailer)
    Route::get('/contato', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contato', [ContactController::class, 'store'])->name('contact.store')
        ->middleware('throttle:6,1');

    // Chat com médicos (feed de publicações)
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/publicacao/{publication}', [ChatController::class, 'show'])->name('chat.show');

    // Conversas 1-a-1
    Route::get('/conversas', [ChatController::class, 'conversations'])->name('chat.conversations');
    Route::get('/conversas/{conversation}', [ChatController::class, 'conversation'])
        ->name('chat.conversation');
    Route::get('/conversas/{conversation}/mensagens', [ChatController::class, 'messages'])
        ->name('chat.messages');
    Route::post('/conversas/{conversation}/enviar', [ChatController::class, 'sendMessage'])
        ->name('chat.sendMessage');
    Route::get('/conversar/{doctor}', [ChatController::class, 'startWith'])->name('chat.start');

    // Área do médico
    Route::middleware('doctor')->prefix('medico')->name('doctor.')->group(function () {
        Route::get('/publicar', [ChatController::class, 'create'])->name('chat.create');
        Route::post('/publicar', [ChatController::class, 'store'])->name('chat.store');
    });

    // Painel administrativo
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/aprovar/{user}', [AdminController::class, 'approve'])->name('approve');
        Route::post('/recusar/{user}', [AdminController::class, 'reject'])->name('reject');
        Route::post('/contato/{contact}', [AdminController::class, 'toggleContact'])->name('contact.toggle');
    });
});
