#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <ncurses.h>

// Estructuras de datos
typedef struct {
    char codigo[11];
    char nombre[51];
    char apellido[51];
    int grado;
    char grupo;
} Alumno;

typedef struct {
    char codigo[11];
    char descripcion[51];
    float costo;
} Concepto;

typedef struct {
    char codigo[11];
    char fecha[11];
    char alumno[11];
    char concepto[11];
    int cantidad;
    float total;
} Ingreso;

// Tabla de conceptos
Concepto conceptos[] = {
    {"DCH", "Desayuno", 50.00},
    {"DG", "Desayuno Grande", 70.00},
    {"COMIDA", "Comida", 80.00},
    {"MEDIA", "Media", 40.00},
    {"ESTANCIA5", "Estancia 5", 100.00},
    {"ESTANCIA7", "Estancia 7", 120.00},
    {"TAREA5", "Tarea 5", 60.00},
    {"TAREA7", "Tarea 7", 80.00},
    {"ESTMES5", "Est. Mes 5", 500.00},
    {"ESTMES7", "Est. Mes 7", 700.00}
};

#define NUM_CONCEPTOS 10

// Variables globales
float total = 0.0;
float efectivo = 0.0;
float cambio = 0.0;
char alumno_buscar[51] = "";
char concepto_sel[11] = "";
int cantidad = 0;

// Prototipos de funciones
void inicializar_pantalla();
void mostrar_menu();
void procesar_entrada();
void buscar_concepto();
void calcular_cambio();
void guardar_ingreso();

int main() {
    // Inicializar ncurses
    initscr();
    cbreak();
    noecho();
    keypad(stdscr, TRUE);

    // Bucle principal
    while (1) {
        mostrar_menu();
        procesar_entrada();
    }

    // Finalizar ncurses
    endwin();
    return 0;
}

void mostrar_menu() {
    clear();
    
    // Título
    mvprintw(1, 1, "SISTEMA DE INGRESOS");
    
    // Búsqueda de alumno
    mvprintw(3, 1, "BUSCAR ALUMNO: ");
    mvprintw(3, 16, "%s", alumno_buscar);
    
    // Lista de conceptos
    mvprintw(5, 1, "CONCEPTOS DISPONIBLES:");
    for (int i = 0; i < NUM_CONCEPTOS; i++) {
        mvprintw(7 + i, 1, "%d. %s - %s", i + 1, conceptos[i].codigo, conceptos[i].descripcion);
    }
    
    // Campos de entrada
    mvprintw(18, 1, "SELECCIONE CONCEPTO: ");
    mvprintw(18, 21, "%s", concepto_sel);
    
    mvprintw(20, 1, "CANTIDAD: ");
    mvprintw(20, 11, "%d", cantidad);
    
    mvprintw(22, 1, "TOTAL ACTUAL: $%.2f", total);
    
    mvprintw(24, 1, "EFECTIVO: $%.2f", efectivo);
    
    mvprintw(26, 1, "CAMBIO: $%.2f", cambio);
    
    refresh();
}

void procesar_entrada() {
    int ch = getch();
    
    switch(ch) {
        case '1'...'9':
            // Selección de concepto
            int idx = ch - '1';
            if (idx < NUM_CONCEPTOS) {
                strcpy(concepto_sel, conceptos[idx].codigo);
                cantidad = 1;
                total += conceptos[idx].costo;
                calcular_cambio();
            }
            break;
            
        case '0':
            // Selección del décimo concepto
            if (NUM_CONCEPTOS == 10) {
                strcpy(concepto_sel, conceptos[9].codigo);
                cantidad = 1;
                total += conceptos[9].costo;
                calcular_cambio();
            }
            break;
            
        case 'e':
        case 'E':
            // Entrada de efectivo
            mvprintw(24, 11, "        ");
            mvprintw(24, 11, "");
            echo();
            scanw("%f", &efectivo);
            noecho();
            calcular_cambio();
            break;
            
        case 'q':
        case 'Q':
            // Salir del programa
            endwin();
            exit(0);
            break;
            
        case KEY_BACKSPACE:
        case 127:
            // Limpiar selección
            strcpy(concepto_sel, "");
            cantidad = 0;
            total = 0.0;
            efectivo = 0.0;
            cambio = 0.0;
            break;
    }
}

void calcular_cambio() {
    cambio = efectivo - total;
}

void guardar_ingreso() {
    FILE *fp;
    Ingreso ingreso;
    time_t t;
    struct tm *tm_info;
    
    // Obtener fecha actual
    time(&t);
    tm_info = localtime(&t);
    strftime(ingreso.fecha, 11, "%Y-%m-%d", tm_info);
    
    // Generar código único
    sprintf(ingreso.codigo, "ING%06d", rand() % 1000000);
    
    // Copiar datos
    strcpy(ingreso.alumno, alumno_buscar);
    strcpy(ingreso.concepto, concepto_sel);
    ingreso.cantidad = cantidad;
    ingreso.total = total;
    
    // Guardar en archivo
    fp = fopen("ingresos.dat", "ab");
    if (fp != NULL) {
        fwrite(&ingreso, sizeof(Ingreso), 1, fp);
        fclose(fp);
    }
} 