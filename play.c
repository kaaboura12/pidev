#include <stdio.h>
#include <stdlib.h>
#include <SDL/SDL.h>
#include <SDL/SDL_image.h>
#include <SDL/SDL_mixer.h>
#include <SDL/SDL_ttf.h>
#include "play.h"
	
void initPerso(personne* p1)
{
    p1->sprite = IMG_Load("css_sprites(2).png");
    if (p1->sprite == NULL)
    {
        printf("Unable to load sprite: %s\n", IMG_GetError());
        return;
    }

    p1->posSprite.x = 0;
    p1->posSprite.y = 0;
    p1->posSprite.w = 4784/16;
    p1->posSprite.h = 960/4;

    p1->posScreen.x = 300;
    p1->posScreen.y = 630;
    
    p1->vitesse=0.01; 
    p1->acceleration=0;
    p1->up=0;

}


void afficherPerso(personne p, SDL_Surface* screen)
{
    SDL_BlitSurface(p.sprite, &p.posSprite, screen, &p.posScreen);
}

void saut(personne* p,int posinit) 
 {
    if (p->up == 1) {
        
        //SDL_Delay(5);
   	p->posRelative.x+=20;
	p->posRelative.y= (-0.001) * p->posRelative.x * p->posRelative.x+150;
        //SDL_Delay(5);
       	p->posScreen.x=p->px + p->posRelative.x+100;
        if(p->posinit >= p->py - p->posRelative.y)
		p->posScreen.y=p->py - p->posRelative.y;
        else
         	p->posScreen.y=p->posinit;  

    }
    
}

void animerPerso(personne* p)
{
    int largeurSprite = 4784;
    int hauteurSprite = 960;

    if (p->posSprite.x == largeurSprite - p->posSprite.w)
    {
        p->posSprite.x = 0;
    }
    else
    {
        p->posSprite.x += p->posSprite.w;
    }

    p->posSprite.y = p->posSprite.h * p->direction;
}


void movePerso_right (personne *p, Uint32 dt)
{
    double dx;
    dx=1/2*(p->acceleration)*dt*dt+p->vitesse * dt;
    p->posScreen.x=p->posScreen.x + dx;

    animerPerso(p);
}

void movePerso_left (personne *p, Uint32 dt)
{
    double dx;
    dx=1/2*(p->acceleration)*dt*dt+p->vitesse * dt;
    p->posScreen.x=p->posScreen.x -dx;

    animerPerso(p);
}
void liberer(personne *p)
{
    SDL_FreeSurface(p->sprite);
}


//////////////////////////////////
void renderText(SDL_Surface *surface, TTF_Font *font, SDL_Color color, char *text, int x, int y)
{
    SDL_Rect destRect;
    destRect.x = x;
    destRect.y = y;

    SDL_Surface *textSurface = TTF_RenderText_Blended(font, text, color);

    SDL_BlitSurface(textSurface, NULL, surface, &destRect);
}




void enterPlayerName(char playerName[], SDL_Surface *screen, int *continuer)
{

    TTF_Font *font = NULL;
    font = TTF_OpenFont("Retro.ttf", 24);

    SDL_Color textColor = {255, 255, 255};

    SDL_Event event;

    int loop = 1;
    while (loop)
    {

        SDL_FillRect(screen, NULL, 0x000000);
        renderText(screen, font, textColor, "Choose a name: ", 10, 10);

        while (SDL_PollEvent(&event))
        {
            switch (event.type)
            {
            case SDL_QUIT:
                loop = 0;
                *continuer = 0;
                break;

            case SDL_KEYDOWN:
                if (strlen(playerName) < 20)
                {
                    if (event.key.keysym.sym >= SDLK_a && event.key.keysym.sym <= SDLK_z)
                    {
                        strncat(playerName, SDL_GetKeyName(event.key.keysym.sym), 1);
                    }
                }
                if (event.key.keysym.sym == SDLK_BACKSPACE && strlen(playerName) > 0)
                {
                    playerName[strlen(playerName) - 1] = '\0';
                }
                if (event.key.keysym.sym == SDLK_RETURN && strlen(playerName) > 0)
                {
                    loop = 0;
                }
                break;
            }
        }

        renderText(screen, font, textColor, playerName, 200, 50);

        SDL_Flip(screen);
    }
    TTF_CloseFont(font);
}


void initBack(background *b)
{
    int i, w;
    b->image[0] = IMG_Load("stage1.png");
    b->image[1] = IMG_Load("stage2.png");
    b->image[2] = IMG_Load("stage3.png");
    b->stageLoaded = 0;
    b->camera.x = 0;
    b->camera.y = 0;
    b->camera.h = 2000;
    b->camera.w = 2000;

    b->animation.spriteSheet = IMG_Load("clock.png");
    b->animation.frames = 5;
    b->animation.clipLoaded = 0;
    for (i = 0, w = 0; i < b->animation.frames; i++, w += 500)
    {
        b->animation.Clips[i].w = 300; // reduce the width of each frame to make the clock smaller
        b->animation.Clips[i].h = 325; // reduce the height of each frame to make the clock smaller
        b->animation.Clips[i].x = 20 + (i * 300); // move the clock to the top-left corner and add some padding
        b->animation.Clips[i].y = 30;
    }
}



void afficherback(background b, SDL_Surface *screen)
{
    SDL_BlitSurface(b.image[b.stageLoaded], &b.camera, screen, NULL);

    SDL_BlitSurface(b.animation.spriteSheet, &b.animation.Clips[b.animation.clipLoaded], screen, NULL);
}




void scrolling(background *b, int direction, int pasAvancement)
{
    switch (direction)
    {
    case 1:
        b->camera.x += pasAvancement;
        break;

    case 2:
        if (b->camera.x >= 0)
            b->camera.x -= pasAvancement;
        break;
    case 3:
        if (b->camera.y >= 0)
            b->camera.y -= pasAvancement;
        break;

    case 4:
        b->camera.y += pasAvancement;
        break;
    }
}





void saveScore(ScoreInfo s, char nomfichier[])
{
    FILE *f;
    f = fopen(nomfichier, "a");
    if (f != NULL)
        fprintf(f, "%s %d %d\n", s.playerName, s.score, s.temps);
    fclose(f);
}




void bestScore(char *filename, ScoreInfo t[])
{
    FILE *fp = fopen(filename, "r");
    if (fp == NULL)
    {
        return;
    }

    int n = 0;
    while (fscanf(fp, "%s %d %d", t[n].playerName, &t[n].score, &t[n].temps) != EOF)
    {
        n++;
    }

    fclose(fp);

    for (int i = 0; i < n - 1; i++)
    {
        for (int j = i + 1; j < n; j++)
        {
            if (t[i].score < t[j].score || (t[i].score == t[j].score && t[i].temps > t[j].temps))
            {
                ScoreInfo tmp = t[i];
                t[i] = t[j];
                t[j] = tmp;
            }
        }
    }
}





void showBestScore(ScoreInfo t[], SDL_Surface *screen, int *continuer)
{
    TTF_Font *font = NULL;
    font = TTF_OpenFont("Retro.ttf", 12);

    SDL_Color textColor = {255, 255, 255};

    SDL_Surface *leader;
    leader = IMG_Load("best.png");

    int loop = 1;
    SDL_Event event;

    SDL_FillRect(screen, NULL, 0x000000);
    SDL_BlitSurface(leader, NULL, screen, NULL);
    int y = 270;//
    for (int i = 0; i < 3; i++)
    {
        char nom[100];
        char info[100];

        sprintf(nom, "Nom du joueur : %s", t[i].playerName);
        sprintf(info, "Score : %d, Temps : %d", t[i].score, t[i].temps);

        renderText(screen, font, textColor, nom, 320, y);
        renderText(screen, font, textColor, info, 320, y + 50);
        y += 150;
    }
    SDL_Flip(screen);
    while (loop)
    {

        while (SDL_PollEvent(&event))
        {
            switch (event.type)
            {
            case SDL_QUIT:
                loop = 0;
                *continuer = 0;
                break;
            }
        }
    }
}

///////////////////////////////////////////////////////

void initmap(minimap *m)
{
    m->position_mini.x = 0;
    m->position_mini.y = 0;
    m->sprite = NULL;
    m->sprite = IMG_Load("min.png");
    m->dot = IMG_Load("pr.bmp");
    //m->posdot.x = 0;
    //m->posdot.y = 0;
}
void afficherminimap(minimap m, SDL_Surface *screen)
{
    SDL_BlitSurface(m.sprite, NULL, screen, &m.position_mini);
    SDL_BlitSurface(m.dot,NULL,screen,&m.posdot);
}
void free_minimap(minimap m)
{
    SDL_FreeSurface(m.sprite);
    SDL_FreeSurface(m.dot);
}
int majminimap (personne *p, minimap *m ,int camera ,int redimensionnement)
{
    int deplacement=0;
    // Calculate new position of dot based on player's position
    int dotX = p->posScreen.x / 3.45;
    int dotY = p->posScreen.y / 4.66;

    // Update position of dot on minimap
    m->posdot.x = dotX+33;
    m->posdot.y = dotY+5;


    

    if(camera != -1)
    {
        if (camera==0)
        m->posdot.x+=redimensionnement;

        else if (camera==1)
        m->posdot.x-=redimensionnement;

        else if(camera==2)
        m->posdot.y-=redimensionnement;

       /* if (m->posdot.x < m->position_mini.x)
{
    m->posdot.x = m->position_mini.x;
}
else if (m->posdot.x > m->position_mini.x + m->position_mini.w)
{
    m->posdot.x = m->position_mini.x + m->position_mini.w;
}

if (m->posdot.y < m->position_mini.y)
{
    m->posdot.y = m->position_mini.y;
}
else if (m->posdot.y > m->position_mini.y + m->position_mini.h)
{
    m->posdot.y = m->position_mini.y + m->position_mini.h;
}*/


        
    }
            //si on arrive a la fin de l'ecran
        if(m->posdot.x>=814 && m->posdot.x<=890 || m->posdot.x<=58 && m->posdot.x>=0)
        {
            deplacement=1;
        }
        if (m->posdot.x <= 5) {
    m->posdot.x = 5;
}
if (m->posdot.x >= 965) {
    m->posdot.x = 965;
}
if (m->posdot.y <= 0) {
    m->posdot.y = 0;
}
if (m->posdot.y >= 193) {
    m->posdot.y = 193;
}

    return deplacement;
}

		
