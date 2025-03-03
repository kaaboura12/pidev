#include <stdio.h>
#include <stdlib.h>
#include <SDL/SDL.h>
#include <SDL/SDL_image.h>
#include <SDL/SDL_mixer.h>
#include <SDL/SDL_ttf.h>

#include "play.h"
int main(int argc, char** argv)
{
    SDL_Surface* screen = NULL;
    SDL_Surface *button;
    SDL_Rect posButton = {900, 10, 350, 186};
    SDL_Event event;
    int boucle = 1,dir=0,i=0;
    int camera = 0;
    int redimensionnement = 0;
    float t_prev = 0, dt = 0;
    personne p1;
    minimap map;
    temps t;
    
    
    SDL_Init(SDL_INIT_EVERYTHING);
    TTF_Init();
    Mix_OpenAudio(44100, MIX_DEFAULT_FORMAT, 2, 2048);////////////
    screen = SDL_SetVideoMode(1700, 960, 32, SDL_HWSURFACE | SDL_DOUBLEBUF);

    if (!screen)
    {
        printf("Unable to set video mode: %s\n", SDL_GetError());
        return 1;
    }

    initPerso(&p1);
    initmap(&map);
    background b;
    initBack(&b);
 

    button = IMG_Load("button.png");

    ScoreInfo s = {0, 0, ""};
    enterPlayerName(s.playerName, screen, &boucle);

    int startTime = SDL_GetTicks() / 1000;
    Mix_Music *music1 = Mix_LoadMUS("music1.mp3");
    Mix_Music *music2 = Mix_LoadMUS("music2.mp3");

    SDL_EnableKeyRepeat(100,10);
     int currentStage = 1;
    while (boucle)
    {
        int temps = (SDL_GetTicks() / 1000) - startTime;
        afficherback(b, screen);
        SDL_BlitSurface(button, NULL, screen, &posButton);
        int maj=majminimap(&p1,&map,camera,redimensionnement);
        afficherminimap(map,screen);
        if (temps >= 5 && temps <= 50 && temps % 5 == 0)
        {
            b.animation.clipLoaded = temps / 5;
            s.score = b.animation.clipLoaded;
            
        }
        
        t_prev = SDL_GetTicks();
        
        afficherPerso(p1,screen);
        
        if(dir==0  )
        {
            p1.direction=2;
            animerPerso(&p1);
            SDL_Flip(screen);
            SDL_Delay(40);
            
        }
switch (currentStage)
        {
            case 1:
                if (!Mix_PlayingMusic())
                    Mix_PlayMusic(music1, -1); // play music for stage 1 on loop
                break;
            case 2:
                if (!Mix_PlayingMusic())
                    Mix_PlayMusic(music2, -1); // play music for stage 2 on loop
                break;
            
        }
        
        while (SDL_PollEvent(&event))
        {
            
            switch (event.type)
            {
                case SDL_QUIT:
                    boucle = 0;
                    break;
                case SDL_KEYDOWN:
                    switch (event.key.keysym.sym)
                    {
                                case SDL_MOUSEBUTTONDOWN:
            if (event.button.x >= posButton.x && event.button.x <= posButton.x + posButton.w
                && event.button.y >= posButton.y && event.button.y <= posButton.y + posButton.h)
            {
                ScoreInfo t[60];
                bestScore("best.txt", t);
                showBestScore(t, SDL_SetVideoMode(602, 852, 32, SDL_HWSURFACE | SDL_DOUBLEBUF), &boucle);
            }
            break;
                        case SDLK_UP:
                            
                            dir=1;
                            scrolling(&b, 2, -90);
                            if(p1.up==0)
                            {
                            p1.up = 1;
                            p1.posinit=p1.posScreen.y;
                            SDL_Flip(screen);
                           
                            }
                            
                            break;
                        case SDLK_RIGHT:
                             
                             p1.direction = 3;
                             scrolling(&b, 1, 90);
		        
		         if (p1.vitesse >=0.9)
		         {
		         p1.vitesse =0.9;
		         p1.direction=1;
		         SDL_Flip(screen);
		         }
		         else
		        {
                             p1.acceleration +=  0.0001; 
		         p1.vitesse += p1.acceleration*dt ; 
		       
		         }
		         movePerso_right(&p1,dt);
		         SDL_Flip(screen);
		         break;

                        case SDLK_LEFT:
                        
                            p1.direction = 3;
                            scrolling(&b, 1, -90);
		        if (p1.vitesse >=0.9)
		         {
		         p1.vitesse =0.9;
		         p1.direction=1;
		         SDL_Flip(screen);
		         }
		         else
		         {
		         p1.acceleration +=  0.0001; 
		         p1.vitesse += p1.acceleration*dt ; 
		         }
		         movePerso_left(&p1,dt);
		         SDL_Flip(screen);
                            break;
                        case SDLK_s://save sc
                s.temps = temps;
                saveScore(s, "best.txt");
                ScoreInfo t[60];
                bestScore("best.txt", t);
                showBestScore(t, SDL_SetVideoMode(602, 852, 32, SDL_HWSURFACE | SDL_DOUBLEBUF), &boucle);
                break;          
                    }
                    break;
                case SDL_KEYUP:
                    switch (event.key.keysym.sym)
                    {
                        case SDLK_RIGHT:
                            dir=0; 
                            p1.vitesse=0.01;
                            p1.acceleration=0;
                            break;
                        case SDLK_UP:
                            dir=0; 
                            break;
                        case SDLK_LEFT:
                            dir=0; 
                            p1.vitesse=0.01;
                            p1.acceleration=0;
                            break;
                    }
                    break;
            }
        }

        if (p1.up == 1) 
        {
                    p1.direction=0;
		p1.px=p1.posScreen.x;
		p1.py=p1.posScreen.y;
		while(p1.posRelative.x>=0 && p1.posRelative.x<=400)
		{
			saut (&p1,p1.posinit);
			animerPerso(&p1);
                              afficherback(b, screen);
                  		afficherPerso(p1,screen);
                  		afficherminimap(map,screen);
                              SDL_Delay(5);     
                              SDL_Flip(screen);
                              
		}
		 
			p1.posRelative.x=0;
			p1.posRelative.y=0;
			p1.up=0;
			dir=0;
			
	}

        dt = (SDL_GetTicks() - t_prev) ; 
        SDL_Flip(screen);
    }

    liberer(&p1);
    TTF_Quit();

    free_minimap(map);
    SDL_Quit();
    
    return 0;
}

