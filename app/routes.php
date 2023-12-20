<?php

// Define app routes

use App\Action\TGDB\Player\GetPlayerAction;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->get('/', \App\Action\Home\HomeAction::class)->setName('home');

    $app->get("/logout", \App\Action\Auth\LogoutAction::class)->setName("logout");

    $app->group("/auth", function (RouteCollectorProxy $app) {
        //Via forums
        $app->get("/tgforum", \App\Action\Auth\StartForumAuthenticationAction::class)->setName("auth.forum");
        $app->get("/tgforum/success", \App\Action\Auth\FinishForumAuthenticationAction::class)->setName("auth.forum.check");
    });

    $app->group("/rounds", function (RouteCollectorProxy $app) {
        $app->get("[/page/{page:[0-9]+}]", \App\Action\Round\RoundListingAction::class)->setName("rounds");
    });

    $app->group("/round", function (RouteCollectorProxy $app) {
        $app->get("/{id:[0-9]+}", \App\Action\Round\RoundByIdAction::class)->setName("round");
        $app->get("/{round:[0-9]+}/{name:[a-z_]+}", \App\Action\Round\RoundStatAction::class)->setName("round.stat");
    });

    $app->group("/me", function (RouteCollectorProxy $app) {

        //Bans
        $app->get("/bans[/page/{page:[0-9]+}]", \App\Action\Me\Bans\ViewBansAction::class)->setName("me.bans");
        $app->get("/ban/{id:[0-9]+}", \App\Action\Me\Bans\ViewBanAction::class)->setName("me.ban");

        //Notes
        $app->get("/notes[/page/{page:[0-9]+}]", \App\Action\Me\Notes\ViewNotesAction::class)->setName('me.notes');
        $app->get("/note/{id:[0-9]+}", \App\Action\Me\Notes\ViewNoteAction::class)->setName('me.note');

    })->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        $request = $request->withAttribute('user', true);
        $response = $handler->handle($request);
        return $response;
    });

    $app->group("/tgdb", function (RouteCollectorProxy $app) {
        $app->get("", \App\Action\TGDB\TGDBHomeAction::class)->setName("tgdb");

        //Notes
        $app->get("/notes[/page/{page:[0-9]+}]", \App\Action\TGDB\Notes\NotesIndexAction::class)->setName("tgdb.notes");

        $app->get("/notes/{ckey:[a-zA-Z0-9@]+}[/page/{page:[0-9]+}]", \App\Action\TGDB\Notes\NotesByCkeyAction::class)->setName("tgdb.notes.ckey");

        $app->get("/note/{id:[0-9]+}", \App\Action\TGDB\Notes\NoteByIdAction::class)->setName("tgdb.notes.single");

        $app->get("/notesbyadmin/{ckey:[a-zA-Z0-9@]+}[/page/{page:[0-9]+}]", \App\Action\TGDB\Notes\NotesByAdminAction::class)->setName("tgdb.notes.admin");

        //Bans
        $app->get("/bans[/page/{page:[0-9]+}]", \App\Action\TGDB\Bans\BanIndexAction::class)->setName("tgdb.bans");

        $app->get("/ban/{id:[0-9]+}", \App\Action\TGDB\Bans\BanByIdAction::class)->setName("tgdb.ban");

        $app->get("/bans/{ckey:[a-zA-Z0-9@]+}[/page/{page:[0-9]+}]", \App\Action\TGDB\Bans\BansByCkeyAction::class)->setName("tgdb.bans.ckey");

        $app->get("/bansbyadmin/{ckey:[a-zA-Z0-9@]+}[/page/{page:[0-9]+}]", \App\Action\TGDB\Bans\BansByAdminAction::class)->setName("tgdb.bans.admin");

        //Player page
        $app->get('/player/{ckey:[a-zA-Z0-9@]+}', \App\Action\TGDB\Player\GetPlayerAction::class)->setName('tgdb.player');
    })->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        $request = $request->withAttribute('require', 'ADMIN');
        $response = $handler->handle($request);
        return $response;
    });

};
