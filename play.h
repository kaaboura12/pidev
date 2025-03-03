#ifndef MENU_H
#define MENU_H
#include <stdio.h>
#include <stdlib.h>
#include <SDL/SDL.h>
#include <SDL/SDL_image.h>
#include <SDL/SDL_mixer.h>
#include <SDL/SDL_ttf.h>


typedef struct
{
 SDL_Surface *sprite;
 SDL_Rect posScreen;
 SDL_Rect posSprite;
 SDL_Rect posRelative;
 int direction,up,px,py,posinit;
 double vitesse,   acceleration;
}personne;

	
void initPerso(personne *p1);	
	
		
void movePerso_right (personne *p, Uint32 dt);
void movePerso_left (personne *p, Uint32 dt);

void saut(personne *p,int posinit);	
void animerPerso (personne* p);


void afficherPerso(personne p, SDL_Surface * screen);
void liberer(personne *p);

///////////////////////////

typedef struct
{
     int score;
     int temps;
     char playerName[20];
} ScoreInfo;

typedef struct
{
     SDL_Surface *spriteSheet;
     SDL_Rect Clips[8];
     int frames;
     int clipLoaded;
} animation;

typedef struct
{
     animation animation;
     SDL_Surface *image[3];
     int stageLoaded; // 0 stage 1 1 stage 2
     SDL_Rect camera;
} background;

void initBack(background *b);
void afficherback(background b, SDL_Surface *screen);
void scrolling(background *b, int direction, int pasAvancement);
void saveScore(ScoreInfo s, char nomfichier[]);
void bestScore(char *filename, ScoreInfo t[]);
void enterPlayerName(char playerName[], SDL_Surface *screen, int *continuer);
void showBestScore(ScoreInfo t[], SDL_Surface *screen, int *continuer);

//////////////////////////////////////////////////

typedef struct
{
	SDL_Rect position_mini;
	SDL_Surface *sprite;
	SDL_Surface *dot;
	SDL_Rect posdot;
	int deplacement;
} minimap;

typedef struct temps
{
	SDL_Surface *texte;
	SDL_Rect position;

	TTF_Font *police;

	char entree[100];
	int secondesEcoulees;
	SDL_Color couleurBlanche;
	time_t t1, t2;
	int min, sec;
}temps;


void initmap(minimap *m);
void afficherminimap(minimap m, SDL_Surface *screen);
void free_minimap(minimap m);

void initialiser_temps(temps *t);
void afficher_temps(temps *t, background b);
void free_temps(temps *t);

int majminimap (personne *p, minimap *m ,int camera ,int redimensionnement);
#endif
