import java.util.Collection;
import java.util.Iterator;

/**
 * Bag is an unbounded multiset.
 * 
 * A bag is a collection in which the order of elements does not matter, like
 * with a set, but in which an element can occur several times. For example,
 * the <code>[x, y, x]</code> is different from the bag <code>[x, y]</code> but
 * equal to the bag <code>[x, x, y]</code>. We say that <code>x</code> occurs
 * twice in the bag <code>[x, y, x]</code> and <code> y </code> occurs once.
 * The formal model of a bag <code>b</code> with objects of type <code>E</code>
 * is a total function from <code>E</code> to the natural numbers.
 *
 * @absvar <tt>b : E â†’ int</tt>,
 * @absinv <tt>âˆ€ o : E . b(o) â‰¥ 0</tt>
 * @initially <tt>âˆ€ o : E . b(o) = 0</tt>
 */
public interface Bag<E> extends Collection<E> {

    /**
     * Returns a readable representation of the bag elements.
     * @ensures <tt>result =</tt> the elements of <tt>b</tt> in some order.
     */
    String toString();

    /**
     * Returns the number of occurrences of an object in the bag.
     * @ensures <tt>result = b(o)</tt>
     */
    int occurrences(E o);

    /**
     * Returns the number of elements in this bags.
     * @ensures <tt>result = (âˆ‘ o : E . b(o))</tt>
     */
    int size();

    /**
     * Returns <tt>true</tt> if this bag contains no elements.
     *
     * @ensures <tt>result = (âˆ€ o : E . b(o) = 0)</tt>
     */
    boolean isEmpty();

    /**
     * Returns <tt>true</tt> if this bag contains contains the specified
     * element.
     *
     * @ensures <tt>result = (b(o) > 0)</tt>
     */
    boolean contains(Object o);

    /**
     * Returns an iterator over the elements in this bag.  There are no
     * guarantees concerning the order in which the elements are returned.
     * 
     * @ensures <tt>result =</tt> an <tt>Iterator</tt> over the elements in
     *     this bag
     */
    Iterator<E> iterator();

    /**
     * Returns a new array containing all of the elements in this bag. There
     * are no guarantees concerning the order in which the elements are stored
     * in the array.
     *
     * @ensures <tt>result =</tt> new array of length <tt>size()</tt> with all
     *     elements of the bag in some order
     */
    Object[] toArray();

    // Modification Operations
    /**
     * Adds the specified element to the bag. Always returns <tt>true</tt>. It
     * is allowed to add  <tt>null</tt> to the bag.
     * @ensures <tt>b'(o) = b(o) + 1 âˆ§ (âˆ€ p : E . p â‰  o â‡’ b'(p) = b(p))</tt>
     */
    boolean add(E o);

    /**
     * Removes one occurrence of the specified element from the bag, if present.
     * If the bag contains the element, <tt>true</tt> is returned, if
     * it does not, <tt>false</tt> is returned and the bag does not change.
     *
     * @ensures
     *     <tt>(b(o) > 0 â‡’ b'(o) = b(o) - 1 âˆ§ result = true) âˆ§ <br>
     *     (b(o) = 0 â‡’ b'(o) = b(o) âˆ§ result = false) âˆ§ <br>
     *     (âˆ€ p : E . p â‰  o â‡’ b'(p) = b(p))</tt>
     */
    boolean remove(Object o);
}
