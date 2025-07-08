       IDENTIFICATION DIVISION.
       PROGRAM-ID. INGRESO1.
       AUTHOR. YOUR-NAME.
       DATE-WRITTEN. TODAY.
       DATE-COMPILED. TODAY.

       ENVIRONMENT DIVISION.
       CONFIGURATION SECTION.
       SOURCE-COMPUTER. LINUX.
       OBJECT-COMPUTER. LINUX.
       SPECIAL-NAMES.
           DECIMAL-POINT IS COMMA.

       INPUT-OUTPUT SECTION.
       FILE-CONTROL.
           SELECT ALUMNO-FILE ASSIGN TO "alumno.dat"
               ORGANIZATION IS INDEXED
               ACCESS MODE IS DYNAMIC
               RECORD KEY IS ALUMNO-CODIGO
               FILE STATUS IS FS-ALUMNO.

           SELECT CONCEPTOS-FILE ASSIGN TO "conceptos.dat"
               ORGANIZATION IS INDEXED
               ACCESS MODE IS DYNAMIC
               RECORD KEY IS CONCEPTO-CODIGO
               FILE STATUS IS FS-CONCEPTOS.

           SELECT INGRESOS-FILE ASSIGN TO "ingresos.dat"
               ORGANIZATION IS INDEXED
               ACCESS MODE IS DYNAMIC
               RECORD KEY IS INGRESO-CODIGO
               FILE STATUS IS FS-INGRESOS.

       DATA DIVISION.
       FILE SECTION.
       FD  ALUMNO-FILE.
       01  ALUMNO-RECORD.
           05  ALUMNO-CODIGO        PIC X(10).
           05  ALUMNO-NOMBRE        PIC X(50).
           05  ALUMNO-APELLIDO      PIC X(50).
           05  ALUMNO-GRADO         PIC 9(2).
           05  ALUMNO-GRUPO         PIC X(1).

       FD  CONCEPTOS-FILE.
       01  CONCEPTO-RECORD.
           05  CONCEPTO-CODIGO      PIC X(10).
           05  CONCEPTO-DESCRIPCION PIC X(50).
           05  CONCEPTO-COSTO       PIC 9(5)V99.

       FD  INGRESOS-FILE.
       01  INGRESO-RECORD.
           05  INGRESO-CODIGO       PIC X(10).
           05  INGRESO-FECHA        PIC X(10).
           05  INGRESO-ALUMNO       PIC X(10).
           05  INGRESO-CONCEPTO     PIC X(10).
           05  INGRESO-CANTIDAD     PIC 9(3).
           05  INGRESO-TOTAL        PIC 9(5)V99.

       WORKING-STORAGE SECTION.
       01  WS-FILE-STATUS.
           05  FS-ALUMNO            PIC XX.
           05  FS-CONCEPTOS         PIC XX.
           05  FS-INGRESOS          PIC XX.

       01  WS-CONTROLS.
           05  WS-EOF               PIC X VALUE 'N'.
               88  EOF              VALUE 'Y'.
           05  WS-EXIT              PIC X VALUE 'N'.
               88  EXIT-PROGRAM     VALUE 'Y'.

       01  WS-DISPLAY-FIELDS.
           05  WS-TOTAL-ACTUAL      PIC ZZZ,ZZ9,99.
           05  WS-EFECTIVO          PIC ZZZ,ZZ9,99.
           05  WS-CAMBIO            PIC ZZZ,ZZ9,99.

       01  WS-CONCEPTOS-TABLE.
           05  WS-CONCEPTO OCCURS 10 TIMES
               INDEXED BY IDX-CONCEPTO.
               10  WS-CODIGO        PIC X(10).
               10  WS-DESCRIPCION   PIC X(50).
               10  WS-COSTO         PIC 9(5)V99.

       01  WS-TEMPORARY.
           05  WS-ALUMNO-BUSCAR     PIC X(50).
           05  WS-CONCEPTO-SEL      PIC X(10).
           05  WS-CANTIDAD          PIC 9(3).
           05  WS-TOTAL             PIC 9(5)V99.
           05  WS-EFECTIVO-INPUT    PIC 9(5)V99.

       SCREEN SECTION.
       01  MAIN-SCREEN.
           05  BLANK SCREEN.
           05  LINE 1 COL 1 VALUE "SISTEMA DE INGRESOS".
           05  LINE 3 COL 1 VALUE "BUSCAR ALUMNO: ".
           05  LINE 3 COL 16 PIC X(50) TO WS-ALUMNO-BUSCAR.
           05  LINE 5 COL 1 VALUE "CONCEPTOS DISPONIBLES:".
           05  LINE 7 COL 1 VALUE "1. DCH - Desayuno".
           05  LINE 8 COL 1 VALUE "2. DG - Desayuno Grande".
           05  LINE 9 COL 1 VALUE "3. COMIDA - Comida".
           05  LINE 10 COL 1 VALUE "4. MEDIA - Media".
           05  LINE 11 COL 1 VALUE "5. ESTANCIA 5".
           05  LINE 12 COL 1 VALUE "6. ESTANCIA 7".
           05  LINE 13 COL 1 VALUE "7. TAREA 5".
           05  LINE 14 COL 1 VALUE "8. TAREA 7".
           05  LINE 15 COL 1 VALUE "9. EST. MES 5".
           05  LINE 16 COL 1 VALUE "10. EST. MES 7".
           05  LINE 18 COL 1 VALUE "SELECCIONE CONCEPTO: ".
           05  LINE 18 COL 21 PIC X(10) TO WS-CONCEPTO-SEL.
           05  LINE 20 COL 1 VALUE "CANTIDAD: ".
           05  LINE 20 COL 11 PIC 9(3) TO WS-CANTIDAD.
           05  LINE 22 COL 1 VALUE "TOTAL ACTUAL: ".
           05  LINE 22 COL 15 PIC ZZZ,ZZ9,99 FROM WS-TOTAL.
           05  LINE 24 COL 1 VALUE "EFECTIVO: ".
           05  LINE 24 COL 11 PIC 9(5)V99 TO WS-EFECTIVO-INPUT.
           05  LINE 26 COL 1 VALUE "CAMBIO: ".
           05  LINE 26 COL 9 PIC ZZZ,ZZ9,99 FROM WS-CAMBIO.

       PROCEDURE DIVISION.
       MAIN-PROCEDURE.
           PERFORM INITIALIZE-PROGRAM
           PERFORM UNTIL EXIT-PROGRAM
               DISPLAY MAIN-SCREEN
               ACCEPT MAIN-SCREEN
               PERFORM PROCESS-INPUT
           END-PERFORM
           PERFORM TERMINATE-PROGRAM
           STOP RUN.

       INITIALIZE-PROGRAM.
           OPEN INPUT ALUMNO-FILE
           OPEN INPUT CONCEPTOS-FILE
           OPEN I-O INGRESOS-FILE
           MOVE 0 TO WS-TOTAL
           MOVE 0 TO WS-EFECTIVO-INPUT
           MOVE 0 TO WS-CAMBIO
           PERFORM INITIALIZE-CONCEPTOS-TABLE.

       INITIALIZE-CONCEPTOS-TABLE.
           MOVE "DCH" TO WS-CODIGO(1)
           MOVE "Desayuno" TO WS-DESCRIPCION(1)
           MOVE 50,00 TO WS-COSTO(1)
           MOVE "DG" TO WS-CODIGO(2)
           MOVE "Desayuno Grande" TO WS-DESCRIPCION(2)
           MOVE 70,00 TO WS-COSTO(2)
           MOVE "COMIDA" TO WS-CODIGO(3)
           MOVE "Comida" TO WS-DESCRIPCION(3)
           MOVE 80,00 TO WS-COSTO(3)
           MOVE "MEDIA" TO WS-CODIGO(4)
           MOVE "Media" TO WS-DESCRIPCION(4)
           MOVE 40,00 TO WS-COSTO(4)
           MOVE "ESTANCIA5" TO WS-CODIGO(5)
           MOVE "Estancia 5" TO WS-DESCRIPCION(5)
           MOVE 100,00 TO WS-COSTO(5)
           MOVE "ESTANCIA7" TO WS-CODIGO(6)
           MOVE "Estancia 7" TO WS-DESCRIPCION(6)
           MOVE 120,00 TO WS-COSTO(6)
           MOVE "TAREA5" TO WS-CODIGO(7)
           MOVE "Tarea 5" TO WS-DESCRIPCION(7)
           MOVE 60,00 TO WS-COSTO(7)
           MOVE "TAREA7" TO WS-CODIGO(8)
           MOVE "Tarea 7" TO WS-DESCRIPCION(8)
           MOVE 80,00 TO WS-COSTO(8)
           MOVE "ESTMES5" TO WS-CODIGO(9)
           MOVE "Est. Mes 5" TO WS-DESCRIPCION(9)
           MOVE 500,00 TO WS-COSTO(9)
           MOVE "ESTMES7" TO WS-CODIGO(10)
           MOVE "Est. Mes 7" TO WS-DESCRIPCION(10)
           MOVE 700,00 TO WS-COSTO(10).

       PROCESS-INPUT.
           IF WS-CONCEPTO-SEL NOT = SPACES
               PERFORM FIND-CONCEPTO
               IF WS-CONCEPTO-SEL NOT = SPACES
                   COMPUTE WS-TOTAL = WS-TOTAL + 
                       (WS-COSTO(IDX-CONCEPTO) * WS-CANTIDAD)
                   PERFORM CALCULAR-CAMBIO
               END-IF
           END-IF.

       FIND-CONCEPTO.
           SET IDX-CONCEPTO TO 1
           SEARCH WS-CONCEPTO
               AT END
                   MOVE SPACES TO WS-CONCEPTO-SEL
               WHEN WS-CODIGO(IDX-CONCEPTO) = WS-CONCEPTO-SEL
                   CONTINUE
           END-SEARCH.

       CALCULAR-CAMBIO.
           COMPUTE WS-CAMBIO = WS-EFECTIVO-INPUT - WS-TOTAL.

       TERMINATE-PROGRAM.
           CLOSE ALUMNO-FILE
           CLOSE CONCEPTOS-FILE
           CLOSE INGRESOS-FILE. 