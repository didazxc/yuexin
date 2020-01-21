<?php


namespace App\Http\Services;


class Module
{
    const Movies='Movies';
    const MotionCor='MotionCor';
    const CTF='CTF';
    const Pick='Pick';
    const Extract='Extract';

    const Modules=[self::MotionCor,self::CTF,self::Pick,self::Extract];
}