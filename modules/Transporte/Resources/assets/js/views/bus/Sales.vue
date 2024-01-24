<template>
    <div class="container">
        <div class="row">
            <div class="col-8 py-5">
                <button v-on:click="obtener" :data-id="cols * 4 + 1">Click Me</button>
                <button v-on:click="push">Agrega</button>
                <button>Guardar distribución</button>

                <div>
                    <table class="mx-auto">
                        <tr v-for="(idxr, r) in rows">
                            <td v-for="(idxc, c) in cols" class="pl-2" style="width: 100px; height: 70px" @dblclick="onSeatSelected(idxr, idxc, getNumber(idxr, idxc))">
                                <movable v-if="!isAisle(idxr, idxc)" >
                                    <span class="text-transparent">...............</span>
                                <svg  id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" v-if="isSeat(idxr, idxc)">
                                    <path :class="classifier(idxr, idxc)" d="M36,17.3H80.4a8.88,8.88,0,0,1,6.72-7.25A5.77,5.77,0,0,0,81.57,6H36a5.72,5.72,0,0,0-5.76,5.66A5.71,5.71,0,0,0,36,17.3Z"/>
                                    <path :class="classifier(idxr, idxc)" d="M80.29,82.79H36A5.66,5.66,0,1,0,36,94.1H81.47a6.13,6.13,0,0,0,5.44-3.41A8.77,8.77,0,0,1,80.29,82.79Z"/>
                                    <path :class="classifier(idxr, idxc)" d="M80.08,79.7V20.5H35.92A8.85,8.85,0,0,1,27.17,13h-18a4,4,0,0,0-4.06,4V82.79a4,4,0,0,0,4.06,3.95H27.28a8.65,8.65,0,0,1,8.75-7Z"/>
                                    <path :class="classifier(idxr, idxc)" d="M89.15,12.93a5.71,5.71,0,0,0-5.76,5.65V82.15a5.76,5.76,0,0,0,11.52,0V18.58A5.71,5.71,0,0,0,89.15,12.93Z"/>
                                    <path :class="classifier(idxr, idxc)" d="M90.21,9.94a8.93,8.93,0,0,0-8.74-7H36a8.94,8.94,0,0,0-8.75,6.93H9.15A7.22,7.22,0,0,0,2,17V82.79a7.06,7.06,0,0,0,7.15,7h18a8.85,8.85,0,0,0,8.75,7.26H81.47A8.91,8.91,0,0,0,90,90.9a8.81,8.81,0,0,0,8-8.75V18.58A8.84,8.84,0,0,0,90.21,9.94ZM36,6H81.57a5.77,5.77,0,0,1,5.55,4.06A8.88,8.88,0,0,0,80.4,17.3H36a5.71,5.71,0,0,1-5.76-5.65A5.72,5.72,0,0,1,36,6ZM27.28,86.74H9.15a4,4,0,0,1-4.06-3.95V17a4,4,0,0,1,4.06-4h18a8.85,8.85,0,0,0,8.75,7.47H80.08V79.7H36A8.65,8.65,0,0,0,27.28,86.74ZM81.47,94.1H36a5.66,5.66,0,1,1,0-11.31H80.29a8.77,8.77,0,0,0,6.62,7.9A6.13,6.13,0,0,1,81.47,94.1ZM94.91,82.15a5.76,5.76,0,0,1-11.52,0V18.58a5.76,5.76,0,0,1,11.52,0Z"/>
                                    <text x="10" y="20" style="fill:red;" >
                                        <tspan x="30" y="60" font-size="32" :id="idxr">{{getNumber(idxr, idxc)}}</tspan>
                                    </text>
                                </svg>
                                    <div v-else style="width: 60px">
                                        <object class="img-fluid w-100" :data="getImage(r,c)" type="image/svg+xml"></object>
                                    </div>
                                </movable>
                            </td>
                        </tr>
                    </table>
                </div>
<!--                        <movable class="testmove" posTop="222" posLeft="222" shiftKey="true">-->
<!--                        <movable>-->
<!--                            <svg @click="onSeatSelected(seat.position.r, seat.position.c, getNumber(seat.position.r, seat.position.c))"  id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">-->
<!--                                <path :class="classifier(seat.position.r, seat.position.c)" d="M36,17.3H80.4a8.88,8.88,0,0,1,6.72-7.25A5.77,5.77,0,0,0,81.57,6H36a5.72,5.72,0,0,0-5.76,5.66A5.71,5.71,0,0,0,36,17.3Z"/>-->
<!--                                <path :class="classifier(seat.position.r, seat.position.c)" d="M80.29,82.79H36A5.66,5.66,0,1,0,36,94.1H81.47a6.13,6.13,0,0,0,5.44-3.41A8.77,8.77,0,0,1,80.29,82.79Z"/>-->
<!--                                <path :class="classifier(seat.position.r, seat.position.c)" d="M80.08,79.7V20.5H35.92A8.85,8.85,0,0,1,27.17,13h-18a4,4,0,0,0-4.06,4V82.79a4,4,0,0,0,4.06,3.95H27.28a8.65,8.65,0,0,1,8.75-7Z"/>-->
<!--                                <path :class="classifier(seat.position.r, seat.position.c)" d="M89.15,12.93a5.71,5.71,0,0,0-5.76,5.65V82.15a5.76,5.76,0,0,0,11.52,0V18.58A5.71,5.71,0,0,0,89.15,12.93Z"/>-->
<!--                                <path :class="classifier(seat.position.r, seat.position.c)" d="M90.21,9.94a8.93,8.93,0,0,0-8.74-7H36a8.94,8.94,0,0,0-8.75,6.93H9.15A7.22,7.22,0,0,0,2,17V82.79a7.06,7.06,0,0,0,7.15,7h18a8.85,8.85,0,0,0,8.75,7.26H81.47A8.91,8.91,0,0,0,90,90.9a8.81,8.81,0,0,0,8-8.75V18.58A8.84,8.84,0,0,0,90.21,9.94ZM36,6H81.57a5.77,5.77,0,0,1,5.55,4.06A8.88,8.88,0,0,0,80.4,17.3H36a5.71,5.71,0,0,1-5.76-5.65A5.72,5.72,0,0,1,36,6ZM27.28,86.74H9.15a4,4,0,0,1-4.06-3.95V17a4,4,0,0,1,4.06-4h18a8.85,8.85,0,0,0,8.75,7.47H80.08V79.7H36A8.65,8.65,0,0,0,27.28,86.74ZM81.47,94.1H36a5.66,5.66,0,1,1,0-11.31H80.29a8.77,8.77,0,0,0,6.62,7.9A6.13,6.13,0,0,1,81.47,94.1ZM94.91,82.15a5.76,5.76,0,0,1-11.52,0V18.58a5.76,5.76,0,0,1,11.52,0Z"/>-->
<!--                                <text x="10" y="20" style="fill:red;">-->
<!--                                    <tspan x="30" y="60" font-size="32" :id="seat.position.r">{{getNumber(seat.position.r, seat.position.c)}}</tspan>-->
<!--                                </text>-->
<!--                            </svg>-->
<!--                        </movable>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
            <div class="col-4 pt-3">
                <div class="card" v-show="selectedSeat != null" style="display: none">
                    <div class="card-header">
                        Properties
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li @click="changeSeatStatus('RA')" class="list-group-item" :class="seatStatus('RA')">
                                <div class="float-left bg-white" style="width: 25px;">
                                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path class="cls-ra" d="M36,17.3H80.4a8.88,8.88,0,0,1,6.72-7.25A5.77,5.77,0,0,0,81.57,6H36a5.72,5.72,0,0,0-5.76,5.66A5.71,5.71,0,0,0,36,17.3Z"/><path class="cls-ra" d="M80.29,82.79H36A5.66,5.66,0,1,0,36,94.1H81.47a6.13,6.13,0,0,0,5.44-3.41A8.77,8.77,0,0,1,80.29,82.79Z"/><path class="cls-ra" d="M80.08,79.7V20.5H35.92A8.85,8.85,0,0,1,27.17,13h-18a4,4,0,0,0-4.06,4V82.79a4,4,0,0,0,4.06,3.95H27.28a8.65,8.65,0,0,1,8.75-7Z"/><path class="cls-ra" d="M89.15,12.93a5.71,5.71,0,0,0-5.76,5.65V82.15a5.76,5.76,0,0,0,11.52,0V18.58A5.71,5.71,0,0,0,89.15,12.93Z"/><path class="cls-ra" d="M90.21,9.94a8.93,8.93,0,0,0-8.74-7H36a8.94,8.94,0,0,0-8.75,6.93H9.15A7.22,7.22,0,0,0,2,17V82.79a7.06,7.06,0,0,0,7.15,7h18a8.85,8.85,0,0,0,8.75,7.26H81.47A8.91,8.91,0,0,0,90,90.9a8.81,8.81,0,0,0,8-8.75V18.58A8.84,8.84,0,0,0,90.21,9.94ZM36,6H81.57a5.77,5.77,0,0,1,5.55,4.06A8.88,8.88,0,0,0,80.4,17.3H36a5.71,5.71,0,0,1-5.76-5.65A5.72,5.72,0,0,1,36,6ZM27.28,86.74H9.15a4,4,0,0,1-4.06-3.95V17a4,4,0,0,1,4.06-4h18a8.85,8.85,0,0,0,8.75,7.47H80.08V79.7H36A8.65,8.65,0,0,0,27.28,86.74ZM81.47,94.1H36a5.66,5.66,0,1,1,0-11.31H80.29a8.77,8.77,0,0,0,6.62,7.9A6.13,6.13,0,0,1,81.47,94.1ZM94.91,82.15a5.76,5.76,0,0,1-11.52,0V18.58a5.76,5.76,0,0,1,11.52,0Z"/></svg>
                                </div>
                                <span class="px-3">Available</span>
                            </li>
                            <li @click="changeSeatStatus('RB')" class="list-group-item" :class="seatStatus('RB')">
                                <div class="float-left" style="width: 25px;">
                                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path class="cls-rb" d="M36,17.3H80.4a8.88,8.88,0,0,1,6.72-7.25A5.77,5.77,0,0,0,81.57,6H36a5.72,5.72,0,0,0-5.76,5.66A5.71,5.71,0,0,0,36,17.3Z"/><path class="cls-rb" d="M80.29,82.79H36A5.66,5.66,0,1,0,36,94.1H81.47a6.13,6.13,0,0,0,5.44-3.41A8.77,8.77,0,0,1,80.29,82.79Z"/><path class="cls-rb" d="M80.08,79.7V20.5H35.92A8.85,8.85,0,0,1,27.17,13h-18a4,4,0,0,0-4.06,4V82.79a4,4,0,0,0,4.06,3.95H27.28a8.65,8.65,0,0,1,8.75-7Z"/><path class="cls-rb" d="M89.15,12.93a5.71,5.71,0,0,0-5.76,5.65V82.15a5.76,5.76,0,0,0,11.52,0V18.58A5.71,5.71,0,0,0,89.15,12.93Z"/><path class="cls-rb" d="M90.21,9.94a8.93,8.93,0,0,0-8.74-7H36a8.94,8.94,0,0,0-8.75,6.93H9.15A7.22,7.22,0,0,0,2,17V82.79a7.06,7.06,0,0,0,7.15,7h18a8.85,8.85,0,0,0,8.75,7.26H81.47A8.91,8.91,0,0,0,90,90.9a8.81,8.81,0,0,0,8-8.75V18.58A8.84,8.84,0,0,0,90.21,9.94ZM36,6H81.57a5.77,5.77,0,0,1,5.55,4.06A8.88,8.88,0,0,0,80.4,17.3H36a5.71,5.71,0,0,1-5.76-5.65A5.72,5.72,0,0,1,36,6ZM27.28,86.74H9.15a4,4,0,0,1-4.06-3.95V17a4,4,0,0,1,4.06-4h18a8.85,8.85,0,0,0,8.75,7.47H80.08V79.7H36A8.65,8.65,0,0,0,27.28,86.74ZM81.47,94.1H36a5.66,5.66,0,1,1,0-11.31H80.29a8.77,8.77,0,0,0,6.62,7.9A6.13,6.13,0,0,1,81.47,94.1ZM94.91,82.15a5.76,5.76,0,0,1-11.52,0V18.58a5.76,5.76,0,0,1,11.52,0Z"/></svg>
                                </div>
                                <span class="px-3">Booked</span>
                            </li>
                            <li @click="changeSeatStatus('FA')" class="list-group-item" :class="seatStatus('FA')">
                                <div class="float-left" style="width: 25px;">
                                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path class="cls-fa" d="M36,17.3H80.4a8.88,8.88,0,0,1,6.72-7.25A5.77,5.77,0,0,0,81.57,6H36a5.72,5.72,0,0,0-5.76,5.66A5.71,5.71,0,0,0,36,17.3Z"/><path class="cls-fa" d="M80.29,82.79H36A5.66,5.66,0,1,0,36,94.1H81.47a6.13,6.13,0,0,0,5.44-3.41A8.77,8.77,0,0,1,80.29,82.79Z"/><path class="cls-fa" d="M80.08,79.7V20.5H35.92A8.85,8.85,0,0,1,27.17,13h-18a4,4,0,0,0-4.06,4V82.79a4,4,0,0,0,4.06,3.95H27.28a8.65,8.65,0,0,1,8.75-7Z"/><path class="cls-fa" d="M89.15,12.93a5.71,5.71,0,0,0-5.76,5.65V82.15a5.76,5.76,0,0,0,11.52,0V18.58A5.71,5.71,0,0,0,89.15,12.93Z"/><path class="cls-fa" d="M90.21,9.94a8.93,8.93,0,0,0-8.74-7H36a8.94,8.94,0,0,0-8.75,6.93H9.15A7.22,7.22,0,0,0,2,17V82.79a7.06,7.06,0,0,0,7.15,7h18a8.85,8.85,0,0,0,8.75,7.26H81.47A8.91,8.91,0,0,0,90,90.9a8.81,8.81,0,0,0,8-8.75V18.58A8.84,8.84,0,0,0,90.21,9.94ZM36,6H81.57a5.77,5.77,0,0,1,5.55,4.06A8.88,8.88,0,0,0,80.4,17.3H36a5.71,5.71,0,0,1-5.76-5.65A5.72,5.72,0,0,1,36,6ZM27.28,86.74H9.15a4,4,0,0,1-4.06-3.95V17a4,4,0,0,1,4.06-4h18a8.85,8.85,0,0,0,8.75,7.47H80.08V79.7H36A8.65,8.65,0,0,0,27.28,86.74ZM81.47,94.1H36a5.66,5.66,0,1,1,0-11.31H80.29a8.77,8.77,0,0,0,6.62,7.9A6.13,6.13,0,0,1,81.47,94.1ZM94.91,82.15a5.76,5.76,0,0,1-11.52,0V18.58a5.76,5.76,0,0,1,11.52,0Z"/></svg>
                                </div>
                                <span class="px-3">Reserve for Female</span>
                            </li>
                            <li @click="changeSeatStatus('FB')" class="list-group-item" :class="seatStatus('FB')">
                                <div class="float-left" style="width: 25px;">
                                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path class="cls-fb" d="M36,17.3H80.4a8.88,8.88,0,0,1,6.72-7.25A5.77,5.77,0,0,0,81.57,6H36a5.72,5.72,0,0,0-5.76,5.66A5.71,5.71,0,0,0,36,17.3Z"/><path class="cls-fb" d="M80.29,82.79H36A5.66,5.66,0,1,0,36,94.1H81.47a6.13,6.13,0,0,0,5.44-3.41A8.77,8.77,0,0,1,80.29,82.79Z"/><path class="cls-fb" d="M80.08,79.7V20.5H35.92A8.85,8.85,0,0,1,27.17,13h-18a4,4,0,0,0-4.06,4V82.79a4,4,0,0,0,4.06,3.95H27.28a8.65,8.65,0,0,1,8.75-7Z"/><path class="cls-fb" d="M89.15,12.93a5.71,5.71,0,0,0-5.76,5.65V82.15a5.76,5.76,0,0,0,11.52,0V18.58A5.71,5.71,0,0,0,89.15,12.93Z"/><path class="cls-fb" d="M90.21,9.94a8.93,8.93,0,0,0-8.74-7H36a8.94,8.94,0,0,0-8.75,6.93H9.15A7.22,7.22,0,0,0,2,17V82.79a7.06,7.06,0,0,0,7.15,7h18a8.85,8.85,0,0,0,8.75,7.26H81.47A8.91,8.91,0,0,0,90,90.9a8.81,8.81,0,0,0,8-8.75V18.58A8.84,8.84,0,0,0,90.21,9.94ZM36,6H81.57a5.77,5.77,0,0,1,5.55,4.06A8.88,8.88,0,0,0,80.4,17.3H36a5.71,5.71,0,0,1-5.76-5.65A5.72,5.72,0,0,1,36,6ZM27.28,86.74H9.15a4,4,0,0,1-4.06-3.95V17a4,4,0,0,1,4.06-4h18a8.85,8.85,0,0,0,8.75,7.47H80.08V79.7H36A8.65,8.65,0,0,0,27.28,86.74ZM81.47,94.1H36a5.66,5.66,0,1,1,0-11.31H80.29a8.77,8.77,0,0,0,6.62,7.9A6.13,6.13,0,0,1,81.47,94.1ZM94.91,82.15a5.76,5.76,0,0,1-11.52,0V18.58a5.76,5.76,0,0,1,11.52,0Z"/></svg>
                                </div>
                                <span class="px-3">Reserve for Female - Booked</span>
                            </li>
                            <li @click="changeSeatStatus('MA')" class="list-group-item" :class="seatStatus('MA')">
                                <div class="float-left" style="width: 25px;">
                                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path class="cls-ma" d="M36,17.3H80.4a8.88,8.88,0,0,1,6.72-7.25A5.77,5.77,0,0,0,81.57,6H36a5.72,5.72,0,0,0-5.76,5.66A5.71,5.71,0,0,0,36,17.3Z"/><path class="cls-ma" d="M80.29,82.79H36A5.66,5.66,0,1,0,36,94.1H81.47a6.13,6.13,0,0,0,5.44-3.41A8.77,8.77,0,0,1,80.29,82.79Z"/><path class="cls-ma" d="M80.08,79.7V20.5H35.92A8.85,8.85,0,0,1,27.17,13h-18a4,4,0,0,0-4.06,4V82.79a4,4,0,0,0,4.06,3.95H27.28a8.65,8.65,0,0,1,8.75-7Z"/><path class="cls-ma" d="M89.15,12.93a5.71,5.71,0,0,0-5.76,5.65V82.15a5.76,5.76,0,0,0,11.52,0V18.58A5.71,5.71,0,0,0,89.15,12.93Z"/><path class="cls-ma" d="M90.21,9.94a8.93,8.93,0,0,0-8.74-7H36a8.94,8.94,0,0,0-8.75,6.93H9.15A7.22,7.22,0,0,0,2,17V82.79a7.06,7.06,0,0,0,7.15,7h18a8.85,8.85,0,0,0,8.75,7.26H81.47A8.91,8.91,0,0,0,90,90.9a8.81,8.81,0,0,0,8-8.75V18.58A8.84,8.84,0,0,0,90.21,9.94ZM36,6H81.57a5.77,5.77,0,0,1,5.55,4.06A8.88,8.88,0,0,0,80.4,17.3H36a5.71,5.71,0,0,1-5.76-5.65A5.72,5.72,0,0,1,36,6ZM27.28,86.74H9.15a4,4,0,0,1-4.06-3.95V17a4,4,0,0,1,4.06-4h18a8.85,8.85,0,0,0,8.75,7.47H80.08V79.7H36A8.65,8.65,0,0,0,27.28,86.74ZM81.47,94.1H36a5.66,5.66,0,1,1,0-11.31H80.29a8.77,8.77,0,0,0,6.62,7.9A6.13,6.13,0,0,1,81.47,94.1ZM94.91,82.15a5.76,5.76,0,0,1-11.52,0V18.58a5.76,5.76,0,0,1,11.52,0Z"/></svg>
                                </div>
                                <span class="px-3">Reserve for Male</span>
                            </li>
                            <li @click="changeSeatStatus('MB')" class="list-group-item" :class="seatStatus('MB')">
                                <div class="float-left" style="width: 25px;">
                                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path class="cls-mb" d="M36,17.3H80.4a8.88,8.88,0,0,1,6.72-7.25A5.77,5.77,0,0,0,81.57,6H36a5.72,5.72,0,0,0-5.76,5.66A5.71,5.71,0,0,0,36,17.3Z"/><path class="cls-mb" d="M80.29,82.79H36A5.66,5.66,0,1,0,36,94.1H81.47a6.13,6.13,0,0,0,5.44-3.41A8.77,8.77,0,0,1,80.29,82.79Z"/><path class="cls-mb" d="M80.08,79.7V20.5H35.92A8.85,8.85,0,0,1,27.17,13h-18a4,4,0,0,0-4.06,4V82.79a4,4,0,0,0,4.06,3.95H27.28a8.65,8.65,0,0,1,8.75-7Z"/><path class="cls-mb" d="M89.15,12.93a5.71,5.71,0,0,0-5.76,5.65V82.15a5.76,5.76,0,0,0,11.52,0V18.58A5.71,5.71,0,0,0,89.15,12.93Z"/><path class="cls-mb" d="M90.21,9.94a8.93,8.93,0,0,0-8.74-7H36a8.94,8.94,0,0,0-8.75,6.93H9.15A7.22,7.22,0,0,0,2,17V82.79a7.06,7.06,0,0,0,7.15,7h18a8.85,8.85,0,0,0,8.75,7.26H81.47A8.91,8.91,0,0,0,90,90.9a8.81,8.81,0,0,0,8-8.75V18.58A8.84,8.84,0,0,0,90.21,9.94ZM36,6H81.57a5.77,5.77,0,0,1,5.55,4.06A8.88,8.88,0,0,0,80.4,17.3H36a5.71,5.71,0,0,1-5.76-5.65A5.72,5.72,0,0,1,36,6ZM27.28,86.74H9.15a4,4,0,0,1-4.06-3.95V17a4,4,0,0,1,4.06-4h18a8.85,8.85,0,0,0,8.75,7.47H80.08V79.7H36A8.65,8.65,0,0,0,27.28,86.74ZM81.47,94.1H36a5.66,5.66,0,1,1,0-11.31H80.29a8.77,8.77,0,0,0,6.62,7.9A6.13,6.13,0,0,1,81.47,94.1ZM94.91,82.15a5.76,5.76,0,0,1-11.52,0V18.58a5.76,5.76,0,0,1,11.52,0Z"/></svg>
                                </div>
                                <span class="px-3">Reserve for Male - Booked</span>
                            </li>
                            <li @click="quit(1,1, true)" class="list-group-item" :class="seatStatus('RB')">
                                <div class="float-left" style="width: 25px;">
                                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path class="cls-rb" d="M36,17.3H80.4a8.88,8.88,0,0,1,6.72-7.25A5.77,5.77,0,0,0,81.57,6H36a5.72,5.72,0,0,0-5.76,5.66A5.71,5.71,0,0,0,36,17.3Z"/><path class="cls-rb" d="M80.29,82.79H36A5.66,5.66,0,1,0,36,94.1H81.47a6.13,6.13,0,0,0,5.44-3.41A8.77,8.77,0,0,1,80.29,82.79Z"/><path class="cls-rb" d="M80.08,79.7V20.5H35.92A8.85,8.85,0,0,1,27.17,13h-18a4,4,0,0,0-4.06,4V82.79a4,4,0,0,0,4.06,3.95H27.28a8.65,8.65,0,0,1,8.75-7Z"/><path class="cls-rb" d="M89.15,12.93a5.71,5.71,0,0,0-5.76,5.65V82.15a5.76,5.76,0,0,0,11.52,0V18.58A5.71,5.71,0,0,0,89.15,12.93Z"/><path class="cls-rb" d="M90.21,9.94a8.93,8.93,0,0,0-8.74-7H36a8.94,8.94,0,0,0-8.75,6.93H9.15A7.22,7.22,0,0,0,2,17V82.79a7.06,7.06,0,0,0,7.15,7h18a8.85,8.85,0,0,0,8.75,7.26H81.47A8.91,8.91,0,0,0,90,90.9a8.81,8.81,0,0,0,8-8.75V18.58A8.84,8.84,0,0,0,90.21,9.94ZM36,6H81.57a5.77,5.77,0,0,1,5.55,4.06A8.88,8.88,0,0,0,80.4,17.3H36a5.71,5.71,0,0,1-5.76-5.65A5.72,5.72,0,0,1,36,6ZM27.28,86.74H9.15a4,4,0,0,1-4.06-3.95V17a4,4,0,0,1,4.06-4h18a8.85,8.85,0,0,0,8.75,7.47H80.08V79.7H36A8.65,8.65,0,0,0,27.28,86.74ZM81.47,94.1H36a5.66,5.66,0,1,1,0-11.31H80.29a8.77,8.77,0,0,0,6.62,7.9A6.13,6.13,0,0,1,81.47,94.1ZM94.91,82.15a5.76,5.76,0,0,1-11.52,0V18.58a5.76,5.76,0,0,1,11.52,0Z"/></svg>
                                </div>
                                <span class="px-3">Quitar</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
.numeros{
    position: relative;
    top: -30px;
    left: 12px;
}
</style>
<style>.cls-selected{fill:green;stroke:#000;stroke-miterlimit:10;}</style>
<style>.cls-ra{fill:#fff;stroke:#000;stroke-miterlimit:10;}</style>
<style>.cls-rb{fill:gray;stroke:#000;stroke-miterlimit:10;}</style>
<style>.cls-fa{fill:#fff;stroke:red;stroke-miterlimit:10;}</style>
<style>.cls-fb{fill:pink;stroke:red;stroke-miterlimit:10;}</style>
<style>.cls-ma{fill:#fff;stroke:blue;stroke-miterlimit:10;}</style>
<style>.cls-mb{fill:royalblue;stroke:blue;stroke-miterlimit:10;}</style>
<script>

export default {
    name: "Sales",
    data() {
        return {
            op: 0,
            errors: [],
            o: [],
            selectedSeat: null,
            rows: 5,
            c: 1,
            r: 6,
            cols: 12,
            seats: [],
            array:[],
        }
    },
    computed: {
        draggingInfo() {
            return this.dragging ? "under drag" : "";
        }
    },
    methods:
        {
            push(){
                let count = this.rows * this.cols;
                if (count == this.seats.length + 11){
                    this.rows = this.rows + 1;
                }
                if(this.c == 13){
                    this.c = 1;
                    this.r = this.r + 1;
                }
                this.seats.push({
                    type: 1,
                    position: {r: this.r, c: this.c},
                    status: "RA",
                });
                this.c = this.c + 1;

            },
            getNumber(idx, idy){
                let result;
                let column = idy - 1;
                let yyy = column * (this.rows -1);
                let cols = this.cols - 1;
                let final = cols * 4;
                let yyx = yyy - 1;
                result = idx > 2 ? idx + yyx: idx + yyy;
                if (idy == this.cols){
                    result = idx + final;
                }
                return result;
            },
            moviendo(idt){

            },
            getSeat(r, c) {
                for(let i = 0; i < this.seats.length; ++i) {
                    if(this.seats[i].position.r == r && this.seats[i].position.c == c) {
                        return this.seats[i];
                    }
                }
                return null;
            },
            generateSeats() {
                for(let y = 1; y <= this.rows; ++y){
                    for(let x = 1; x <= this.cols; ++x) {
                        if (!this.isAisle(y, x)) {
                            this.seats.push({
                                type: 2,
                                position: {r: y, c: x},
                                status: "RA",
                            });
                        }
                    }
                }
            },
            classifier(r, c){
                let seat = this.getSeat(r, c);
                if(seat != null){
                    if(this.selectedSeat != seat) {
                        switch(seat.status) {
                            case 'RA':
                                return 'cls-ra';
                            case 'RB':
                                return 'cls-rb';
                            case 'FA':
                                return 'cls-fa';
                            case 'FB':
                                return 'cls-fb';
                            case 'MA':
                                return 'cls-ma';
                            case 'MB':
                                return 'cls-mb';
                            default:
                                return 'cls-ra';
                        }
                    } else {
                        return 'cls-selected';
                    }
                }
            },
            isAisle(r, c) {
                if(r == 3){
                    if(c >= 1 && c < this.cols) {
                        return true;
                    }
                }
                if(r == this.rows && this.seats.length > 49){
                    if(c > this.c - 1){
                        return true;
                    }
                }
                return false;
            },
            isSeat(r, c) {
                for(let i = 0; i < this.seats.length; ++i) {
                    if(this.seats[i].position.r == r && this.seats[i].position.c == c) {
                        if(this.seats[i].type > 1){
                            return false;
                        }
                    }
                }
                return true;
            },
            getImage(r,c){
                for(let i = 0; i < this.seats.length; ++i) {
                    if(this.seats[i].position.r == r && this.seats[i].position.c == c) {
                        if(this.seats[i].type == 2){
                            return '/images/bano.svg';
                        }
                    }
                }
            },
            onSeatSelected: function(r,c,number) {
                if(this.selectedSeat == this.getSeat(r, c)) {
                    this.selectedSeat = null;
                } else {
                    this.selectedSeat = this.getSeat(r, c);
                }

            },
            obtener: function (event){
                var num = event.target.getAttribute('data-id');
            },
            seatStatus(status){
                if(this.selectedSeat != null) {
                    if(this.selectedSeat.status == status)
                        return 'active';
                }
                return '';
            },
            changeSeatStatus(status) {
                if(this.selectedSeat != null) {
                    for(let i = 0; i < this.seats.length; ++i) {
                        if(this.seats[i].position.r == this.selectedSeat.position.r && this.seats[i].position.c == this.selectedSeat.position.c) {
                            this.seats[i].status = status;
                            this.selectedSeat = null;
                            break;
                        }
                    }
                }
            },
        },
    beforeMount() {
    },
    mounted() {
        this.generateSeats();
    }
}

</script>

<style scoped>

</style>
