<?php

class Matrix
{
    private $arr = [];

    public function getArray() {
        return $this->arr;
    }

    public function setArray(array $arr) {
        $this->arr = $this->convert($arr);
        return $this;
    }

    public function __construct(array $arr) {
        return $this->setArray($arr);
    }

    public function add(Matrix $matrix) {
        $matrixArr = $matrix->getArray();
        $arr = $this->getArray();

        $maxWidth = $this->countWidth($arr) > $this->countWidth($matrixArr)
            ? $this->countWidth($arr) : $this->countWidth($matrixArr);

        $maxHeight = $this->countHeight($arr) > $this->countHeight($matrixArr)
            ? $this->countHeight($arr) : $this->countHeight($matrixArr);

        for($i = 0; $i < $maxHeight; $i++)
        {
            for($j = 0; $j < $maxWidth; $j++)
            {
                $arr[$i][$j] += $matrixArr[$i][$j];
            }
        }

        $this->setArray($arr);
        return $this;
    }

    public function diff(Matrix $matrix) {
        $matrixArr = $matrix->getArray();
        $arr = $this->getArray();

        $maxWidth = $this->countWidth($arr) > $this->countWidth($matrixArr)
            ? $this->countWidth($arr) : $this->countWidth($matrixArr);

        $maxHeight = $this->countHeight($arr) > $this->countHeight($matrixArr)
            ? $this->countHeight($arr) : $this->countHeight($matrixArr);

        for($i = 0; $i < $maxHeight; $i++)
        {
            for($j = 0; $j < $maxWidth; $j++)
            {
                $arr[$i][$j] -= $matrixArr[$i][$j];
            }
        }

        $this->setArray($arr);
        return $this;
    }

    public function mult(Matrix $matrix) {
        $arr = $this->getArray();
        $matrixArr = $matrix->getArray();

        if($this->countWidth($arr) != $this->countHeight($matrixArr))
            return;

        $newArr = [];
        $matrixArrWidth = $this->countWidth($matrixArr);
        $matrixArrHeight = $this->countHeight($matrixArr);

        for($i = 0; $i < $matrixArrHeight; $i++)
        {
            $newArr[$i] = [];
            for($j = 0; $j < $matrixArrWidth; $j++)
            {
                $num = 0;
                for($k = 0; $k < $matrixArrHeight; $k++)
                {
                    $num += ($arr[$j][$k] * $arr[$k][$j]);
                }
                $newArr[$i][] = $num;
            }
        }

        $this->setArray($newArr);
        return $this;
    }

    public function toArray() : array {
        return $this->getArray();
    }

    private function convert(array $arr) {
        $convertedArr = [];

        $maxWidth = 0;
        $maxHeight = 0;
        for($i = 0; $i < count($arr); $i++)
        {
            if(!is_array($arr[$i]))
                $arr[$i] = [$arr[$i]];

            if($maxWidth < count($arr[$i]))
                $maxWidth = count($arr[$i]);

            $maxHeight++;
        }

        for($i = 0; $i < $maxHeight; $i++)
        {
            $convertedArr[] = [];
            for($j = 0; $j < $maxWidth; $j++)
            {
                $convertedArr[$i][] = $arr[$i][$j];

                if($convertedArr[$i][$j] == null)
                    $convertedArr[$i][$j] = 0;
            }
        }

        return $convertedArr;
    }

    private function countWidth(array $arr) : int {
        $maxWidth = 0;
        for ($i = 0; $i < count($arr); $i++)
        {
            if(!is_array($arr[$i]))
                $arr[$i] = [$arr[$i]];
            if($maxWidth < count($arr[$i]))
                $maxWidth++;
        }

        return $maxWidth;
    }

    private function countHeight(array $arr) : int {
        return count($arr);
    }
}