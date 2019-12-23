<?php

class DoublyLinkedListNode
{
    /**
     * @var DoublyLinkedListNode|null
     */
    public $next;

    /**
     * @var DoublyLinkedListNode|null
     */
    public $prev;

    /**
     * @var mixed
     */
    public $value;

    /**
     * DoublyLinkedListNode constructor
     *
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }
}

class DoublyLinkedList implements \IteratorAggregate
{
    /**
     * @var DoublyLinkedListNode
     */
    private $head;

    /**
     * @var DoublyLinkedListNode
     */
    private $tail;

    /**
     * @var int
     */
    private $size = 0;

    /**
     * DoublyLinkedList constructor
     */
    public function __construct()
    {
        $this->head = new DoublyLinkedListNode();
        $this->tail = new DoublyLinkedListNode();

        $this->head->next = $this->tail;
        $this->tail->prev = $this->head;
    }

    /**
     * @param DoublyLinkedListNode $node
     * @return void
     */
    public function pushNode(DoublyLinkedListNode $node): void
    {
        $next = $this->head->next;
        $node->prev = $this->head;
        $node->next = $next;

        $this->head->next = $node;
        $next->prev = $node;

        $this->size++;
    }

    /**
     * @param DoublyLinkedListNode $node
     * @return void
     */
    public function deleteNode(DoublyLinkedListNode $node): void
    {
        $node->prev->next = $node->next;
        $node->next->prev = $node->prev;
        $this->size--;
    }

    /**
     * @return DoublyLinkedListNode
     */
    public function bottom(): DoublyLinkedListNode
    {
        return $this->tail->prev;
    }

    /**
     * @return void
     */
    public function deleteLastNode(): void
    {
        $this->deleteNode($this->tail->prev);
    }

    /**
     * @param DoublyLinkedListNode $node
     */
    public function moveToTop(DoublyLinkedListNode $node): void
    {
        $this->deleteNode($node);
        $this->pushNode($node);
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $node = $this->head;
        while ($node !== null) {
            yield $node;
            $node = $node->next;
        }
    }
}

class LRUCache implements \IteratorAggregate
{
    /**
     * @var int
     */
    private $capacity;

    /**
     * @var array
     */
    private $hashMap = [];

    /**
     * @var DoublyLinkedList
     */
    private $list;

    /**
     * @param int $capacity
     */
    public function __construct(int $capacity)
    {
        $this->capacity = $capacity;
        $this->list = new DoublyLinkedList();
    }

    /**
     * @param int $key
     * @return mixed
     */
    public function get($key)
    {
        /** @var DoublyLinkedListNode|null $node */
        $node = $this->hashMap[$key] ?? null;

        if ($node === null) {
            return -1;
        }

        $this->list->moveToTop($node);

        return $node->value[0];
    }

    /**
     * @param int $key
     * @param int $value
     * @return void
     */
    public function put($key, $value): void
    {
        /** @var DoublyLinkedListNode $node */
        $node = $this->hashMap[$key] ?? null;

        if ($node !== null) {
            $node->value = [$value, $key];
            $this->list->moveToTop($node);

            return;
        }

        if ($this->isFull()) {
            /** @var DoublyLinkedListNode $node */
            $node = $this->list->bottom();
            unset($this->hashMap[$node->value[1]]);
            $this->list->deleteLastNode();
        }

        $node = new DoublyLinkedListNode([$value, $key]);

        $this->list->pushNode($node);
        $this->hashMap[$key] = $node;
    }

    /**
     * @return bool
     */
    private function isFull(): bool
    {
        return $this->capacity === $this->list->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        foreach ($this->list as $node) {
            yield $node->value[0];
        }
    }
}

$tests = [
    [1, 1],
    [2, 2],
    [1],
    [3, 3],
    [2],
    [4, 4],
    [1],
    [3],
    [4],
];

$lru = new LRUCache(2);
foreach ($tests as $value) {
    if (\count($value) === 2) {
        $lru->put($value[0], $value[1]);
        echo 'INSERT: ' . $value[0] . PHP_EOL;
    } else {
        echo 'GET ' . $value[0] . '; ' . $lru->get($value[0]) . PHP_EOL;
    }
}

echo '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ' . PHP_EOL;

//$tests = [
//    [1, 1],
//    [2, 2],
//    [1],
//    [3, 3],
//    [2],
//    [4, 4],
//    [1],
//    [3],
//    [4],
//];
//
//$lru = new LRUCache(1);
//foreach ($tests as $value) {
//    if (\count($value) === 2) {
//        $lru->put($value[0], $value[1]);
//        echo 'INSERT: ' . $value[0] . PHP_EOL;
//    } else {
//        echo 'GET ' . $value[0] . '; ' . $lru->get($value[0]) . PHP_EOL;
//    }
//}

