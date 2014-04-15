/*
 *  Life.cpp
 *
 *  Created by Rudie Shahinian on 04/09/10.
 *  Copyright 2010 BrightSide. All rights reserved.
 *
 */

//#include <avr/io.h>
#include "WProgram.h"
#include "Life.h"


life::life() {
        int x = 0;
        int y = 0;
        team1[0] = 1;
        team1[1] = 2;
        team1[2] = 3;
        team1[3] = 4;
        team2[0] = 5;
        team2[1] = 6;
        team2[2] = 7;
        team2[3] = 8;

        for (y=0; y < GRIDSIZE; y++) {
                for (x=0; x < GRIDSIZE; x++) {
                        grid[x][y] = 0;
                }
        }
}
life::~life(void) {

}
void life::clearGrid(void) {
        life();
}
void life::setCell(int x, int y, int state) {
        grid[x-1][y-1] = state;
}
int life::getCell(int x, int y) {
        return grid[x-1][y-1];
}

void life::outputGrid(void) {
        //int x = 0;
        //int y = 0;
        //for (y=(GRIDSIZE -1); y >= 0; y--) {
        //      for (x=0; x < GRIDSIZE; x++) {
        //              Serial.print(" ");
        //              Serial.print(grid[x][y]); 
        //      }
        //      Serial.println();
        //}
        //Serial.println();

}
int life::isInTeam(int s) {
        int i = 0;
        if (s == 0) return 0;
        for (i = 0; i < 4; i++) {

                if (team2[i] == s) {
                        return 2;
                }
                else if (team1[i] == s) {
                        return 1;
                }
        }
        return 0;
}
void life::getCellNeighbours(int *neighbours, int x,int y) {
        //int *neighbours = new int[9];

        int i = 0;

        int j = 0;
        int xt = 0;
        int yt = 0;

        for (i = 0; i<=8; i++) {
                neighbours[i] = 0;
        }

        for (i=x-2; i<=x; i++) {

                //if (i>=0 && i<= GRIDSIZE - 1) {

                        for (j=y-2; j<=y; j++) {

                                if (i < 0) {
                                        xt = GRIDSIZE-1;
                                }
                                else {
                                        xt = i % (GRIDSIZE);
                                }

                                if (j < 0) {
                                        yt = GRIDSIZE-1;
                                }
                                else {
                                        yt = j%(GRIDSIZE);
                                }

                                //if (j >= 0 && j <= GRIDSIZE-1) {

                                        if (!(i == x-1 && j == y-1)) {
                                                neighbours[grid[xt][yt]]++;

                                        }

                                //}

                        }

                //}

        }

        //return  neighbours;
}
int life::randState(int t1, int t2, int t3) {
        int retStat = 0;
        //srand((unsigned)time(NULL));
        randomSeed(analogRead(0));
        if (t1 != 0 && t2 != 0 && t3 != 0) {
                while (t1 != retStat && t2 != retStat && t3 != retStat) {
                        retStat = random(1,8); //((rand()%8)+1 );
                }
        }
        else if (t1 != 0 && t2 != 0 && t3 == 0) {
                while (t1 != retStat && t2 != retStat) {
                        retStat = random(1,8); //  ((rand()%8)+1 );
                }
        }
        return retStat;

}
void life::next(void) {
        int x = 0;
        int y = 0;
        int i = 0;
        int team1tot = 0;
        int team2tot = 0;
        int tot = 0;
        int t1 = 0;
        int t2 = 0;
        int t3 = 0;
        int newGrid[GRIDSIZE][GRIDSIZE];
        int neighbours[9];

        for (x = 0; x < GRIDSIZE; x++) {
                for (y = 0; y < GRIDSIZE; y++) {
                        team1tot = 0;
                        team2tot = 0;
                        t1 = 0;
                        t2 = 0;
                        t3 = 0;
                        tot = 0;
                        getCellNeighbours(neighbours, x+1,y+1);
                        //under/over populated - more than 3 or less than 2 neighbours
                        //if over populated die
                        tot = neighbours[1] + neighbours[2] + neighbours[3] + neighbours[4] + neighbours[5] + neighbours[6] + neighbours[7] + neighbours[8];
                        if (tot > 3 || tot < 2 ) {
                                newGrid[x][y] = 0;
                        }
                        //if 2 stay the same, if 3 reproduce
                        else {
                                for (i = 1; i <= 4; i++) {
                                        team1tot = team1tot + neighbours[i];
                                }
                                for (i = 5; i <= 8; i++) {
                                        team2tot = team2tot + neighbours[i];
                                }

                                //if 2 neighbours, stay the same if same team, or die if different
                                if ((team2tot + team1tot) == 2) {
                                        if (team2tot == team1tot) {
                                                //die
                                                newGrid[x][y] = 0;
                                        }
                                        else {
                                                newGrid[x][y] = grid[x][y];
                                        }
                                }
                                //if 3 neighbours, if all three are same reproduce
                                // if 2:1 stay the same
                                // if 1:2 die.
                                else {
                                        if (team2tot == 3 || team1tot == 3) {
                                                for (i = 1; i <= 8; i++) {
                                                        if (neighbours[i] == 3) {
                                                                t1 = i;
                                                        }
                                                        if (neighbours[i] == 2) {
                                                                if (t1 == 0) {
                                                                        t1 = i;
                                                                }
                                                                else {
                                                                        t2 = i;
                                                                }
                                                        }
                                                        else if (neighbours[i] == 1) {
                                                                if (t1 == 0) {
                                                                        t1 = i;
                                                                }
                                                                else if (t2 == 0){
                                                                        t2 = i;
                                                                }
                                                                else if (t3 == 0){
                                                                        t3 = i; 
                                                                }
                                                        }

                                                }
                                                if (t2 == 0) {
                                                        newGrid[x][y] = t1;
                                                }
                                                else {
                                                        newGrid[x][y] = randState(t1,t2, t3);
                                                }

                                        }
                                        else if(team2tot == 2 || team1tot == 2){
                                                for (i = 1; i <= 8; i++) {
                                                        if (neighbours[i] == 2) {
                                                                if (isInTeam(grid[x][y]) == isInTeam(i)){
                                                                        newGrid[x][y] = grid[x][y];
                                                                }
                                                                else {
                                                                        newGrid[x][y] = 0;
                                                                }
                                                        }
                                                }
                                        }

                                }

                        }

                }
        }

        for (x=0; x < GRIDSIZE; x++) {
                for (y=0; y < GRIDSIZE; y++) {
                        grid[x][y] = newGrid[x][y];
                        //Serial.print(newGrid[x][y]);
                }
                //Serial.println("");
        }
        //Serial.println("");

}
